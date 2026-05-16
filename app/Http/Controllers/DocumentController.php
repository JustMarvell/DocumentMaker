<?php

namespace App\Http\Controllers;

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
        return storage_path('app/cached_result/' . $filename);
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
                ->latest('requested_at')
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
                'checkbox' => 'boolean',
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
                return response()->json(['error' => 'Konversi PDF gagal. Mohon coba lagi. Jika masalah masih berlanjut, Periksa log server atau konfigurasi iLoveAPI.'], 500);
            }

            $pdfPath = $this->cachedResultPath($pdfFilename);
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

        $outputPath = storage_path('app/cached_result/' . $uniqueFilename);
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


    // // letter of assignment
    // private function generateLetterOfAssignment(Request $request, DocumentType $documentType) {
    //     $request->validate([
    //         'la_employee_name'      => 'required|string',
    //         'la_employee_position'  => 'required|string',
    //         'la_employee_address'   => 'required|string',
    //         'la_letter_number'      => 'required|string',
    //         'la_letter_date'        => 'required|date',
    //         'la_assignment_objective' => 'required|string',
    //         'la_destination_agency' => 'required|string',
    //         'la_departure_date'     => 'required|date',
    //         'la_return_date'        => 'required|date',
    //     ]);

    //     $args = [
    //         '--letter-number'              => $request->la_letter_number,
    //         '--assignment-objective'       => $request->la_assignment_objective,
    //         '--destination-agency-location'=> $request->la_destination_agency,
    //         '--employee-name'              => $request->la_employee_name,
    //         '--employee-position'          => $request->la_employee_position,
    //         '--employee-address'           => $request->la_employee_address,
    //         '--departure-date'             => $request->la_departure_date,
    //         '--return-date'                => $request->la_return_date,
    //         '--letter-date'                => $request->la_letter_date,
    //     ];

    //     return $this->runPythonScript($documentType, $args);
    // }

    // private function generatePermissionLetter(Request $request, DocumentType $documentType) {
    //     $request->validate([
    //         'pl_employee_name'     => 'required|string',
    //         'pl_employee_position' => 'required|string',
    //         'pl_employee_address'  => 'required|string',
    //         'pl_letter_date'       => 'required|date',
    //         'pl_target_name'       => 'required|string',
    //         'pl_target_address'    => 'required|string',
    //         'pl_total_sick_day'    => 'required|integer|min:1',
    //         'pl_start_date'        => 'required|date',
    //         'pl_end_date'          => 'required|date|after_or_equal:pl_start_date',
    //     ]);

    //     $args = [
    //         '--employee-name'      => $request->pl_employee_name,
    //         '--employee-position'  => $request->pl_employee_position,
    //         '--employee-address'   => $request->pl_employee_address,
    //         '--employee-id-number' => $request->pl_employee_id_number ?? '',
    //         '--letter-address'     => $request->pl_letter_address ?? '',
    //         '--letter-date'        => $request->pl_letter_date,
    //         '--attachment-count'   => $request->pl_attachment_count ?? '0',
    //         '--target-name'        => $request->pl_target_name,
    //         '--target-address'     => $request->pl_target_address,
    //         '--total-sick-day'     => $request->pl_total_sick_day,
    //         '--start-date'         => $request->pl_start_date,
    //         '--end-date'           => $request->pl_end_date,
    //     ];

    //     return $this->runPythonScript($documentType, $args);
    // }

    // private function generateEmployeePerformanceTargets(Request $request, DocumentType $documentType) {
    //     $request->validate([
    //         'ept_appraisal_period_start' => 'required|date',
    //         'ept_appraisal_period_end' => 'required|date|after_or_equal:ept_appraisal_period_start',
    //         'ept_employee_name' => 'required|string',
    //         'ept_employee_nip' => 'required|string',
    //         'ept_employee_rank' => 'required|string',
    //         'ept_employee_position' => 'required|string',
    //         'ept_employee_work_unit' => 'required|string',
    //         'ept_appraisal_name' => 'required|string',
    //         'ept_appraisal_nip' => 'required|string',
    //         'ept_appraisal_rank' => 'required|string',
    //         'ept_appraisal_position' => 'required|string',
    //         'ept_appraisal_work_unit' => 'required|string',
    //         'ept_leadership_work_result_plan' => 'required|string',
    //         'ept_work_result_plan' => 'required|string',
    //         'ept_work_quantity_indicator' => 'required|string',
    //         'ept_work_quality_indicator' => 'required|string',
    //         'ept_work_time_indicator' => 'required|string',
    //         'ept_work_quantity_target' => 'required|string',
    //         'ept_work_quality_target' => 'required|string',
    //         'ept_work_time_target' => 'required|string',
    //         'ept_additional_work_behaviour_1' => 'required|string',
    //         'ept_additional_work_behaviour_1_description' => 'required|string',
    //         'ept_leadership_spesific_expectation' => 'required|string',
    //     ]);

    //     $args = [
    //         '--appraisal-period-start' => $request->ept_appraisal_period_start,
    //         '--appraisal-period-end' => $request->ept_appraisal_period_end,
    //         '--employee-name' => $request->ept_employee_name,
    //         '--employee-nip' => $request->ept_employee_nip,
    //         '--employee-rank' => $request->ept_employee_rank,
    //         '--employee-position' => $request->ept_employee_position,
    //         '--employee-work-unit' => $request->ept_employee_work_unit,
    //         '--appraisal-name' => $request->ept_appraisal_name,
    //         '--appraisal-nip' => $request->ept_appraisal_nip,
    //         '--appraisal-rank' => $request->ept_appraisal_rank,
    //         '--appraisal-position' => $request->ept_appraisal_position,
    //         '--appraisal-work-unit' => $request->ept_appraisal_work_unit,
    //         '--leadership-work-result-plan' => $request->ept_leadership_work_result_plan,
    //         '--work-result-plan' => $request->ept_work_result_plan,
    //         '--work-quantity-indicator' => $request->ept_work_quantity_indicator,
    //         '--work-quality-indicator' => $request->ept_work_quality_indicator,
    //         '--work-time-indicator' => $request->ept_work_time_indicator,
    //         '--work-quantity-target' => $request->ept_work_quantity_target,
    //         '--work-quality-target' => $request->ept_work_quality_target,
    //         '--work-time-target' => $request->ept_work_time_target,
    //         '--additional-work-behaviour-1' => $request->ept_additional_work_behaviour_1,
    //         '--additional-work-behaviour-1-description' => $request->ept_additional_work_behaviour_1_description,
    //         '--leadership-spesific-expectation' => $request->ept_leadership_spesific_expectation,
    //     ];

    //     return $this->runPythonScript($documentType, $args);
    // }

    // /**
    //  * shared function to run python script
    //  */
    // private function runPythonScript(DocumentType $documentType, array $args) {

    //     $pythonBin = base_path('venv/bin/python');
    //     $scriptPath = base_path("scripts/{$documentType->script_name}");

    //     $ext = pathinfo($documentType->output_filename, PATHINFO_EXTENSION);
    //     $basename = $ext ? pathinfo($documentType->output_filename, PATHINFO_FILENAME) : $documentType->output_filename;
    //     $extension = $ext ?: pathinfo($documentType->template_filename, PATHINFO_EXTENSION);
    //     $uniqueFilename = $basename . '_' . Str::uuid() . '.' . $extension;

    //     $args['--output-filename'] = $uniqueFilename;

    //     $cmd = [$pythonBin, $scriptPath];
    //     foreach ($args as $flag => $value) {
    //         $cmd[] = $flag;
    //         $cmd[] = (string) $value;
    //     }

    //     $process = new Process($cmd);
    //     $process->setTimeout(60);
    //     $process->run();

    //     $status = $process->isSuccessful() ? 'success' : 'failed';

    //     DocumentLog::create([
    //         'user_id' => auth()->id(),
    //         'document_type_id' => $documentType->id,
    //         'output_filename' => $uniqueFilename,
    //         'status' => $status,
    //         'generated_at' => now(),
    //     ]);

    //     if (!$process->isSuccessful()) {
    //         Log::error("Document Generation Failed [{$documentType->script_name}]: " . $process->getErrorOutput());
    //         return back()->with('error', 'Gagal Membuat surat. Silahkan Coba Lagi atau Hubungi Administrator.');
    //     }

    //     $downloadUrl = route('document.download', ['filename' => $uniqueFilename]);

    //     return back()
    //         ->with('success', 'Surat Berhasil di Buat!')
    //         ->with('download_url', $downloadUrl);
    // }
}
