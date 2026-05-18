<?php

namespace App\Http\Controllers;

use App\Helpers\StoragePath;
use App\Models\DocumentField;
use App\Models\DocumentLog;
use App\Models\DocumentType;
use App\Models\OfficialData;
use App\Models\StaffData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use App\Services\IlovePdfConverter;
use App\Models\PdfConversionSetting;
use App\Models\DocumentNumberCounter;
use App\Models\SignatureRequest;

class DocumentController extends Controller
{
    private function cachedResultPath(string $filename): string
    {
        return StoragePath::cachedResult($filename);
    }

    public function index() {

        $role = auth()->check() ? auth()->user()->role : 'guest';

        $documentTypes = DocumentType::active()
            ->accessibleBy($role)
            ->orderBy('name')
            ->get();

        $allFields = DocumentField::whereIn('document_type_id', $documentTypes->pluck('id'))
            ->orderBy('sort_order')
            ->get()
            ->groupBy('document_type_id');

        $numberCounters = DocumentNumberCounter::whereIn('document_type_id', $documentTypes->pluck('id'))
            ->where('enabled', true)
            ->get()
            ->keyBy('document_type_id')
            ->map(fn($c) => $c->field_key);

        $signatureRequests = collect();
        $documentHistory = collect();
        if (auth()->check()) {
            $signatureRequests = SignatureRequest::with(['documentLog.documentType', 'official'])
                ->where('user_id', auth()->id())
                ->orderByRaw('COALESCE(requested_at, created_at) DESC')
                ->take(20)
                ->get();

            $documentHistory = DocumentLog::with(['documentType', 'signatureRequests.official'])
                ->where('user_id', auth()->id())
                ->latest('generated_at')
                ->take(50)
                ->get();
        }

        return view('home', compact('documentTypes', 'allFields', 'numberCounters', 'signatureRequests', 'documentHistory'));
    }

    public function generate(Request $request) {
        $request->validate([
            'letter-type' => 'required|string|exists:document_types,key',
        ]);

        $role = auth()->check() ? auth()->user()->role : 'guest';

        $documentType = DocumentType::where('key', $request->input('letter-type'))
            ->active()
            ->accessibleBy($role)
            ->with('fields')
            ->firstOrFail();

        $rules = [];

        foreach ($documentType->fields as $field) {
            if ($field->is_group_child)
                continue;

            // Loop types — validate as array of IDs
            if (in_array($field->field_type, ['staff_loop', 'official_loop'])) {
                $rules["field_{$field->field_key}"] = $field->is_required
                    ? 'required|array|min:1'
                    : 'nullable|array';
                $rules["field_{$field->field_key}.*"] = 'integer';
                continue;
            }

            if ($field->field_type === 'repeating_group') {
                $rules["field_{$field->field_key}"] = 'nullable|array';
                $children = $documentType->fields
                    ->where('is_group_child', true)
                    ->where('group_key', $field->field_key);
                foreach ($children as $child) {
                    $rules["field_{$field->field_key}.*.{$child->field_key}"] =
                        $child->is_required ? 'required|string' : 'nullable|string';
                }
                continue;
            }

            $rule = $field->is_required ? 'required' : 'nullable';
            $rule .= '|' . match ($field->field_type) {
                'date' => 'date',
                'number' => 'numeric',
                'checkbox' => 'nullable|boolean',
                default => 'string',
            };
            $rules["field_{$field->field_key}"] = $rule;
        }


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // $validator = Validator::make($request->all(), $rules);
        // if ($validator->fails()) {
        //     dd([
        //         'errors' => $validator->errors()->toArray(),
        //         'rules' => $rules,
        //         'request_data' => $request->all(),
        //         'role' => $role,
        //         'doctype' => $documentType->key,
        //     ]);
        // }

        $context = [];

        foreach ($documentType->fields as $field) {
            if ($field->is_group_child)
                continue;

            if ($field->field_type === 'staff_loop') {
                // Fetch selected staff records in the order the user chose
                $selectedIds = $request->input("field_{$field->field_key}", []);
                $staff = StaffData::whereIn('id', $selectedIds)->get()->keyBy('id');
                $context[$field->field_key] = collect($selectedIds)
                    ->map(fn($id) => $staff[$id] ?? null)
                    ->filter()
                    ->map(fn($s) => $s->toArray())
                    ->values()
                    ->toArray();
                continue;
            }

            if ($field->field_type === 'official_loop') {
                $selectedIds = $request->input("field_{$field->field_key}", []);
                $officials = OfficialData::whereIn('id', $selectedIds)->get()->keyBy('id');
                $context[$field->field_key] = collect($selectedIds)
                    ->map(fn($id) => $officials[$id] ?? null)
                    ->filter()
                    ->map(fn($o) => $o->toArray())
                    ->values()
                    ->toArray();
                continue;
            }

            if ($field->field_type === 'repeating_group') {
                $context[$field->field_key] = $request->input("field_{$field->field_key}", []);
                continue;
            }

            if ($field->field_type === 'checkbox') {
                $context[$field->field_key] = (bool) $request->input("field_{$field->field_key}", false);
                continue;
            }

            if ($field->field_type === 'date') {
                $raw = $request->input("field_{$field->field_key}", '');
                $context[$field->field_key] = $raw
                    ? \Carbon\Carbon::parse($raw)->locale('id')->translatedFormat('d F Y')
                    : '';
                continue;
            }

            $context[$field->field_key] = $request->input("field_{$field->field_key}", '');
        }

        return $this->runScript($documentType, $context);

    }

    public function download(string $filename) {
        $filename = basename($filename);
        $path = $this->cachedResultPath($filename);

        abort_unless(file_exists($path), 404);

        DocumentLog::where('output_filename', $filename)
            ->whereNull('downloaded_at')
            ->update(['downloaded_at' => now()]);

        return response()->download($path)->deleteFileAfterSend(false);
    }

    public function preview(string $filename)
    {
        $filename = basename($filename);
        $sourcePath = $this->cachedResultPath($filename);

        if (!file_exists($sourcePath)) {
            return response()->json(['error' => 'File dokumen tidak ditemukan. Mungkin sudah terhapus.'], 404);
        }

        $pdfFilename = pathinfo($filename, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = $this->cachedResultPath($pdfFilename);

        if (request()->boolean('retry') && file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        if (!file_exists($pdfPath)) {
            $setting = PdfConversionSetting::instance();

            if (!$setting->hasQuota()) {
                return response()->json(['error' => 'Kuota konversi PDF bulan ini telah habis. Hubungi administrator.'], 503);
            }

            if (!$setting->iloveapi_public_key || !$setting->iloveapi_secret_key) {
                return response()->json(['error' => 'API key iLoveAPI belum dikonfigurasi. Hubungi administrator.'], 500);
            }

            $converter = new IlovePdfConverter();
            $pdfFilename = $converter->convert($filename);

            if (!$pdfFilename) {
                return response()->json(['error' => 'Konversi PDF gagal. Server iLoveAPI tidak merespons atau file tidak valid.'], 500);
            }

            $pdfPath = $this->cachedResultPath($pdfFilename);
        }

        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'Konversi PDF gagal. File output tidak ditemukan.'], 500);
        }

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
        ]);
    }


    private function runScript(DocumentType $documentType, array $context)
    {
        $signaturePlaceholders = [
            'ttd_pejabat' => $documentType->signature_enabled ? '{{ ttd_pejabat }}' : '',
            'qr_code' => $documentType->signature_enabled ? '{{ qr_code }}' : '',
            'nama_pejabat' => $documentType->signature_enabled ? '{{ nama_pejabat }}' : '',
            'jabatan_pejabat' => $documentType->signature_enabled ? '{{ jabatan_pejabat }}' : '',
            'tgl_ttd' => $documentType->signature_enabled ? '{{ tgl_ttd }}' : '',
        ];

        $context = array_merge($signaturePlaceholders, $context);

        $counter = $documentType->numberCounter;
        $previewNum = null;
        if ($counter && $counter->enabled) {
            $previewNum = $counter->previewNext();
            $context[$counter->field_key] = $previewNum;
        }

        $pythonBin = base_path('venv/bin/python');
        $scriptPath = base_path("scripts/{$documentType->script_name}");
        $extension = pathinfo($documentType->template_filename, PATHINFO_EXTENSION);
        $uniqueFilename = Str::slug($documentType->output_filename) . '_' . Str::uuid() . '.' . $extension;

        $cmd = [
            $pythonBin,
            $scriptPath,
            '--template',
            $documentType->template_filename,
            '--output-filename',
            $uniqueFilename,
            '--context',
            json_encode($context, JSON_UNESCAPED_UNICODE),
        ];

        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();

        $status = $process->isSuccessful() ? 'success' : 'failed';

        $log = DocumentLog::create([
            'user_id' => auth()->id(),
            'document_type_id' => $documentType->id,
            'output_filename' => $uniqueFilename,
            'status' => $status,
            'generated_at' => now(),
        ]);


        if (!$process->isSuccessful()) {
            Log::error("Generation failed [{$documentType->script_name}]: " . $process->getErrorOutput());
            return back()->with('error', 'Gagal membuat dokumen. Silakan coba lagi atau hubungi administrator.');
        }

        $outputPath = StoragePath::cachedResult($uniqueFilename);
        if (!file_exists($outputPath)) {
            Log::error("Generated file not found at: {$outputPath}");
            return back()->with('error', 'Dokumen berhasil diproses tapi file tidak ditemukan.');
        }

        if ($counter && $counter->enabled && $status === 'success') {
            $counter->generateNext();
        }

        $downloadUrl = route('document.download', ['filename' => $uniqueFilename]);
        $previewUrl = null;

        // Generate PDF preview
        if ($documentType->preview_enabled) {
            $previewUrl = route('document.preview', ['filename' => $uniqueFilename]);
        }

        return back()
            ->with('success', 'Dokumen berhasil dibuat!')
            ->with('download_url', $downloadUrl)
            ->with('preview_url', $previewUrl)
            ->with('signature_log_id', $log->id);
    }
}
