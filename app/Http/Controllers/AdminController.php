<?php

namespace App\Http\Controllers;

use App\Models\DocumentField;
use App\Models\DocumentLog;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\StaffData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DocumentNumberCounter;

class AdminController extends Controller
{
    public function index() {

        $totalGenerated = DocumentLog::where('status', 'success')->count();
        $totalFailed = DocumentLog::where('status', 'failed')->count();
        $totalUsers = User::count();

        $perType = DocumentType::withCount([
            'documentLogs as success_count' => fn($q) => $q->where('status', 'success'),
            'documentLogs as failed_count' => fn($q) => $q->where('status', 'failed'),
        ])->get();

        $recentLogs = DocumentLog::with(['user', 'documentType'])
            ->latest('generated_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalGenerated',
            'totalFailed',
            'totalUsers',
            'perType',
            'recentLogs',
        ));

    }

    // comment

    public function logs(Request $request) { 

        $query = DocumentLog::with('user', 'documentType', 'status' , 'signatureRequest')
            ->latest('generated_at');
        
        if ($request->filled('type')) {
            $query->whereHas('documentType', fn($q) =>
                $q->where('key', $request->type));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        };

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(20)->withQueryString();
        $documentTypes = DocumentType::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $status = DocumentLog::orderBy('name')->get();

        return view('admin.logs', compact('logs', 'documentTypes', 'users', 'status'));
    }

    public function users(Request $request) {
        $users = User::withCount('documentLogs')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function updateUserRole(Request $request, User $user) {
        $request->validate([
            'role' => 'required|in:guest,staff,admin',
        ]);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role anda sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Pengguna {$name} berhasil dihapus.");
    }

    public function documentTypes() {
        $documentTypes = DocumentType::withCount('documentLogs')
            ->orderBy('name')
            ->get();

        return view('admin.document-types', compact('documentTypes'));
    }

    public function toggleDocumentType(DocumentType $documentType) {
        $documentType->update(['is_active' => !$documentType->is_active]);
        $status = $documentType->is_active ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'isActive' => $documentType->is_active, 'message' => "{$documentType->name} berhasil {$status}."]);
        }

        return back()->with('success', "{$documentType->name} berhasil {$status}.");
    }

    public function createDocumentType() {
        return view('admin.document-type-create');
    }

    public function storeDocumentType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|unique:document_types,key|regex:/^[a-z0-9\-]+$/',
            'access_level' => 'required|in:guest,staff',
            'file_type' => 'required|in:docx,xlsx',
            'staff_autofill_role' => 'required|in:none,employee,appraiser,both',
            'template_file' => 'required|file|mimes:docx,xlsx,vnd.openxmlformats-officedocument.wordprocessingml.document,vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:10240',
        ]);

        $file = $request->file('template_file');
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug($request->key) . '.' . $extension;
        $file->move(base_path('document_templates'), $filename);

        $documentType = DocumentType::create([
            'name' => $request->name,
            'key' => $request->key,
            'script_name' => $extension === 'xlsx' ? 'xlsx_generator.py' : 'docx_generator.py',
            'template_filename' => $filename,
            'output_filename' => Str::slug($request->key),
            'access_level' => $request->access_level,
            'file_type' => $request->file_type,
            'staff_autofill_role' => $request->staff_autofill_role,
            'is_active' => true,
            'signature_enabled' => false,
        ]);

        return redirect()
            ->route('admin.document-types.fields', $documentType)
            ->with('success', "Template '{$documentType->name}' berhasil ditambahkan. Sekarang tambahkan field.");
    }


    // Show re-upload form
    public function reuploadTemplateForm(DocumentType $documentType)
    {
        return view('admin.document-type-reupload', compact('documentType'));
    }

    // Handle re-upload
    public function reuploadTemplate(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'template_file' => 'required|file|mimes:docx,xlsx,vnd.openxmlformats-officedocument.wordprocessingml.document,vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:10240',
        ]);

        $file = $request->file('template_file');
        $extension = $file->getClientOriginalExtension();

        // Delete the old template file if it exists
        $oldPath = base_path('document_templates/' . $documentType->template_filename);
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }

        // Save the new file using the same base key so the filename stays consistent
        $newFilename = Str::slug($documentType->key) . '.' . $extension;
        $file->move(base_path('document_templates'), $newFilename);

        // Update the document type record
        $documentType->update([
            'template_filename' => $newFilename,
            'file_type' => $extension === 'xlsx' ? 'xlsx' : 'docx',
            'script_name' => $extension === 'xlsx' ? 'xlsx_generator.py' : 'docx_generator.py',
        ]);

        return redirect()
            ->route('admin.document-types.fields', $documentType)
            ->with('success', "Template '{$documentType->name}' berhasil diperbarui. Field yang ada tetap tersimpan.");
    }

    public function manageFields(DocumentType $documentType)
    {
        $fields = $documentType->fields()->get();
        $slots = $documentType->slots()->orderBy('sort_order')->get();
        $staffColumns = DocumentField::staffColumns();

        $fieldCacheJson = json_encode(
            $fields->keyBy('id')->map(fn($f) => [
                'id' => $f->id,
                'label' => $f->label,
                'field_type' => $f->field_type,
                'field_options' => implode(', ', $f->field_options ?? []),
                'is_required' => (bool) $f->is_required,
                'section_label' => $f->section_label ?? '',
                'staff_autofill_column' => $f->staff_autofill_column ?? '',
                'autofill_role' => $f->autofill_role ?? 'none',
                'row_group' => $f->row_group,
                'icon' => $f->icon ?? '',
            ]),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
        );

        return view('admin.document-type-fields', compact('documentType', 'fields', 'slots', 'staffColumns', 'fieldCacheJson'));
    }

    public function storeField(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'field_key'             => ['required', 'string', 'regex:/^[a-z0-9_]+$/', 'max:100',
                                        \Illuminate\Validation\Rule::unique('document_fields')->where('document_type_id', $documentType->id)],
            'label'                 => 'required|string|max:255',
            'field_type'            => 'required|in:text,textarea,date,number,select,checkbox,repeating_group,staff_loop,official_loop',
            'field_options'         => 'nullable|string',
            'is_required'           => 'boolean',
            'section_label'         => 'nullable|string|max:255',
            'group_key'             => 'nullable|string|max:100',
            'is_group_child'        => 'boolean',
            'staff_autofill_column' => 'nullable|string',
            'autofill_role'         => 'nullable|string|max:100',
            'row_group'             => 'nullable|integer|min:1',
            'icon'                  => ['nullable', 'string', 'max:100', function($attribute, $value, $fail)
            {
                if ($value && !in_array($value, array_merge(...array_values(\App\Models\DocumentField::availableIcons())))) {
                    // just allow
                    // uncomment if want hard fail
                    // $fail('Icon tidak valid');
                }
            }],            
        ]);
 
        $fieldOptions = null;
        if ($request->field_type === 'select' && $request->filled('field_options')) {
            $fieldOptions = array_map('trim', explode(',', $request->field_options));
        }
 
        $maxOrder = $documentType->fields()->max('sort_order') ?? 0;

        $field = DocumentField::create([
            'document_type_id'      => $documentType->id,
            'field_key'             => $request->field_key,
            'label'                 => $request->label,
            'field_type'            => $request->field_type,
            'field_options'         => $fieldOptions,
            'is_required'           => $request->boolean('is_required'),
            'sort_order'            => $maxOrder + 1,
            'row_group'             => $request->filled('row_group') ? (int) $request->row_group : null, 
            'section_label'         => $request->section_label,
            'group_key'             => $request->group_key,
            'is_group_child'        => $request->boolean('is_group_child'),
            'staff_autofill_column' => $request->staff_autofill_column ?: null,
            'autofill_role'         => $request->autofill_role ?? 'none',
            'icon'                  => $request->icon ?: null,
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'field' => $field]);
        }
 
        return back()->with('success', "Field '{$request->label}' berhasil ditambahkan.");
    }


    public function updateField(Request $request, DocumentType $documentType, DocumentField $field)
    {
        $request->validate([
            'label'                 => 'required|string|max:255',
            'field_type'            => 'required|in:text,textarea,date,number,select,checkbox,repeating_group,staff_loop,official_loop',
            'field_options'         => 'nullable|string',
            'is_required'           => 'boolean',
            'section_label'         => 'nullable|string|max:255',
            'staff_autofill_column' => 'nullable|string',
            'autofill_role'         => 'nullable|string|max:100',
            'row_group'             => 'nullable|integer|min:1',
            'icon'                  => 'nullable|string|max:100'
        ]);
 
        $fieldOptions = $field->field_options;
        if ($request->field_type === 'select' && $request->filled('field_options')) {
            $fieldOptions = array_map('trim', explode(',', $request->field_options));
        }

        $field->update([
            'label'                 => $request->label,
            'field_type'            => $request->field_type,
            'field_options'         => $fieldOptions,
            'is_required'           => $request->boolean('is_required'),
            'sort_order'            => $field->sort_order,
            'row_group'             => $request->filled('row_group') ? (int) $request->row_group : null,
            'section_label'         => $request->section_label,
            'staff_autofill_column' => $request->staff_autofill_column ?: null,
            'autofill_role'         => $request->autofill_role ?? 'none',
            'icon'                  => $request->icon ?: null,
        ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'field' => $field->fresh()]);
        }
 
        return back()->with('success', "Field '{$field->label}' berhasil diperbarui.");
    }


    public function destroyField(DocumentType $documentType, DocumentField $field)
    {
        $label = $field->label;
        $field->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Field '{$label}' berhasil di hapus."]);
        }

        return back()->with('success', "Field '{$label}' berhasil dihapus.");
    }

    public function reorderFields(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:document_fields,id',
        ]);
 
        foreach ($request->order as $sortOrder => $fieldId) {
            DocumentField::where('id', $fieldId)
                ->where('document_type_id', $documentType->id)
                ->update(['sort_order' => $sortOrder + 1]);
        }
 
        return response()->json(['success' => true]);
    }

    public function staffData(Request $request) {
        $query = StaffData::orderBy('staff_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('staff_name', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('work_unit')) {
            $query->where('work_unit', $request->work_unit);
        }

        if ($request->filled('rank')) {
            $query->where('rank', $request->rank);
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $staffList = $query->paginate(20)->appends($request->query());

        $workUnits = StaffData::distinct()
            ->pluck('work_unit')
            ->filter(fn($value) => !is_null($value))
            ->sort()
            ->values();

        $ranks = StaffData::whereNotNull('rank')
            ->distinct()
            ->pluck('rank')
            ->sort()
            ->values();
            
        $positions = StaffData::whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->sort()
            ->values();

        return view('admin.staff-data', compact('staffList', 'workUnits', 'ranks', 'positions'));
    }

    public function officialData()
    {
        $officialList = \App\Models\OfficialData::orderBy('staff_name')->paginate(20);
        return view('admin.official-data', compact('officialList'));
    }

    public function destroyDocumentType(DocumentType $documentType)
    {
        // Delete the template file
        $templatePath = base_path('document_templates/' . $documentType->template_filename);
        if (file_exists($templatePath)) {
            unlink($templatePath);
        }

        // Fields cascade delete from the migration
        $name = $documentType->name;
        $documentType->delete();

        return redirect()
            ->route('admin.document-types')
            ->with('success', "Template '{$name}' berhasil dihapus.");
    }

    public function manageSlots(DocumentType $documentType)
    {
        $slots = $documentType->slots()->orderBy('sort_order')->get();
        return view('admin.document-type-slots', compact('documentType', 'slots'));
    }

    public function storeSlot(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'slot_key' => [
                'required',
                'string',
                'regex:/^[a-z0-9_]+$/',
                'max:100',
                \Illuminate\Validation\Rule::unique('document_autofill_slots')
                    ->where('document_type_id', $documentType->id)
            ],
            'slot_label' => 'required|string|max:255',
        ]);

        $maxOrder = $documentType->slots()->max('sort_order') ?? 0;

        \App\Models\DocumentAutofillSlot::create([
            'document_type_id' => $documentType->id,
            'slot_key' => $request->slot_key,
            'slot_label' => $request->slot_label,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', "Slot '{$request->slot_label}' berhasil ditambahkan.");
    }

    public function destroySlot(DocumentType $documentType, \App\Models\DocumentAutofillSlot $slot)
    {
        $label = $slot->slot_label;
        $slot->delete();
        return back()->with('success', "Slot '{$label}' berhasil dihapus.");
    }

    public function togglePreview(DocumentType $documentType)
    {
        $documentType->update(['preview_enabled' => !$documentType->preview_enabled]);
        $status = $documentType->preview_enabled ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'preview_enabled' => $documentType->preview_enabled]);
        }

        return back()->with('success', "Preview untuk {$documentType->name} berhasil {$status}.");
    }

    public function guide()
    {
        return view('admin.guide');
    }

    public function guideDownload()
    {
        $path = base_path('resources/docs/SIPADU_Panduan_Penggunaan.docx');

        abort_unless(file_exists($path), 404, 'File panduan tidak ditemukan.');

        return response()->download($path, 'SIPADU_Panduan_Penggunaan.docx');
    }

    public function toggleSignature(DocumentType $documentType)
    {
        $documentType->update(['signature_enabled' => !$documentType->signature_enabled]);
        $status = $documentType->signature_enabled ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'signature_enabled' => $documentType->signature_enabled]);
        }

        return back()->with('success', "Fitur tanda tangan untuk {$documentType->name} berhasil {$status}.");
    }

    public function toggleSignatureImage(DocumentType $documentType) {
        $documentType->update(['signature_use_image' => !$documentType->signature_use_image]);
        $status = $documentType->signature_use_image ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'signature_use_image' => $documentType->signature_use_image]);
        }

        return back()->with('succes', "Embed gambar untuk {$documentType->name} berhasil {$status}.");
    }

    public function toggleSignatureQr(DocumentType $documentType)
    {
        $documentType->update(['signature_use_qr' => !$documentType->signature_use_qr]);
        $status = $documentType->signature_use_qr ? 'diaktifkan' : 'dinonaktifkan';

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'signature_use_qr' => $documentType->signature_use_qr]);
        }

        return back()->with('succes', "Embed QR untuk {$documentType->name} berhasil {$status}.");
    }

    public function numberCounter(DocumentType $documentType) {
        $counter = $documentType->numberCounter;
        $fields = $documentType->fields()->where('is_group_child', false)->get();
        return view('admin.document-type-number-counter', compact('documentType', 'counter', 'fields'));
    }

    public function saveNumberCounter(Request $request, DocumentType $documentType) {
        $request->validate([
            'enabled' => 'boolean',
            'format' => 'required|string|max:255',
            'number_padding' => 'required|string|max:10',
            'reset_on' => 'required|in:never,yearly,monthly',
            'field_key' => 'required|string|max:100',
        ]);

        DocumentNumberCounter::updateOrCreate(
            ['document_type_id' => $documentType->id],
            [
                'enabled' => $request->boolean('enabled'),
                'format' => $request->format,
                'number_padding' => $request->number_padding,
                'reset_on' => $request->reset_on,
                'field_key' => $request->field_key,
            ]
        );

        return back()->with('success', 'Konfigurasi Nomor surat berhasil disimpan.');
    }

    public function setNumberCounter(Request $request, DocumentType $documentType) {
        $request->validate(['current_number' => 'required|integer|min:0']);

        $counter = DocumentNumberCounter::firstOrCreate(
            ['document_type_id' => $documentType->id],
            ['format' => '{number}/DPUPR/{roman_month}/{year}', 'field_key' => 'letter_number']
        );

        $counter->update(['current_number' => $request->current_number]);

        return back()->with('success', "Nomor surat disetel ke {$request->current_number}.");
    }

    public function resetNumberCounter(DocumentType $documentType) {
        $counter = $documentType->numberCounter;
        if ($counter) {
            $counter->update([
                'current_number' => 0,
                'last_reset_year' => null,
                'last_reset_month' => null,
            ]);
        }

        return back()->with('success', 'Nomor surat direset ke 0, Nomor berikutnya: 001 (atau sesuai padding).');
    }

    public function scanFields(Request $request, DocumentType $documentType)
    {
        $scanner = new \App\Services\TemplateScanner();
        $detected = $scanner->scan($documentType->template_filename);

        // Filter out already-defined field keys
        $existingKeys = $documentType->fields()->pluck('field_key')->toArray();
        $newVars = array_filter($detected, fn($v) => !in_array($v, $existingKeys));

        return response()->json([
            'detected' => array_values($newVars),
            'existing' => $existingKeys,
            'all' => $detected,
        ]);
    }

    public function bulkStoreFields(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'fields' => 'required|array|min:1',
            'fields.*.field_key' => 'required|string|regex:/^[a-z0-9_]+$/',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,textarea,date,number,select,checkbox,repeating_group,staff_loop,official_loop',
            'fields.*.section_label' => 'nullable|string|max:255',
            'fields.*.is_required' => 'boolean',
        ]);

        $maxOrder = $documentType->fields()->max('sort_order') ?? 0;
        $created = 0;

        foreach ($request->fields as $i => $fieldData) {
            $exists = $documentType->fields()->where('field_key', $fieldData['field_key'])->exists();
            if ($exists)
                continue;

            DocumentField::create([
                'document_type_id' => $documentType->id,
                'field_key' => $fieldData['field_key'],
                'label' => $fieldData['label'],
                'field_type' => $fieldData['field_type'],
                'is_required' => $fieldData['is_required'] ?? false,
                'sort_order' => $maxOrder + $i + 1,
                'section_label' => $fieldData['section_label'] ?? null,
                'autofill_role' => 'none',
            ]);
            $created++;
        }

        return response()->json(['success' => true, 'created' => $created]);
    }

    public function bulkDeleteLogs(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:document_logs,id']);

        $logs = DocumentLog::whereIn('id', $request->ids)->get();
        $deleted = 0;

        foreach ($logs as $log) {
            $dir = public_path('cached_result/');
            $base = pathinfo($log->output_filename, PATHINFO_FILENAME);

            // delete the original file
            $path = $dir . $log->output_filename;
            if (file_exists($path))
                unlink($path);

            // delete the pdf preview if it exists
            $pdfPath = $dir . $base . '.pdf';
            if (file_exists($pdfPath))
                unlink($pdfPath);

            if (is_null($log->deleted_at)) {
                $log->update(['deleted_at' => now()]);
            }
            $deleted++;
        }

        return back()->with('success', "{$deleted} dokumen berhasil dihapus.");
    }

    public function pdfSettings()
    {
        $setting = \App\Models\PdfConversionSetting::instance();
        return view('admin.pdf-settings', compact('setting'));
    }

    public function savePdfSettings(Request $request)
    {
        $request->validate([
            'monthly_limit' => 'required|integer|min:1',
            'reset_on' => 'required|in:monthly,manual',
            'iloveapi_public_key' => 'nullable|string|max:255',
            'iloveapi_secret_key' => 'nullable|string|max:255',
        ]);

        $setting = \App\Models\PdfConversionSetting::instance();
        $setting->update($request->only([
            'monthly_limit',
            'reset_on',
            'iloveapi_public_key',
            'iloveapi_secret_key',
        ]));

        return back()->with('success', 'Pengaturan PDF berhasil disimpan.');
    }

    public function resetPdfCounter()
    {
        \App\Models\PdfConversionSetting::instance()->resetNow();
        return back()->with('success', 'Counter konversi PDF direset ke 0.');
    }
}
