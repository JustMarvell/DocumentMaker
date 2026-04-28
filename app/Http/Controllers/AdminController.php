<?php

namespace App\Http\Controllers;

use App\Models\DocumentField;
use App\Models\DocumentLog;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\StaffData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $query = DocumentLog::with('user', 'documentType', 'status')
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

    public function documentTypes() {
        $documentTypes = DocumentType::withCount('documentLogs')
            ->orderBy('name')
            ->get();

        return view('admin.document-types', compact('documentTypes'));
    }

    public function toggleDocumentType(DocumentType $documentType) {
        $documentType->update(['is_active' => !$documentType->is_active]);
        $status = $documentType->is_active ? 'diaktifkan' : 'dinonaktifkan';

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

        return view('admin.document-type-fields', compact('documentType', 'fields', 'slots', 'staffColumns'));
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

        DocumentField::create([
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
 
        return back()->with('success', "Field '{$field->label}' berhasil diperbarui.");
    }


    public function destroyField(DocumentType $documentType, DocumentField $field)
    {
        $label = $field->label;
        $field->delete();
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
}
