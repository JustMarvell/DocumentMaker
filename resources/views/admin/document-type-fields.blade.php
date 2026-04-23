@extends('admin.layout')

@section('content')

<div class="flex items-center gap-3 mb-2">
    <a href="{{ route('admin.document-types') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Jenis Dokumen</a>
</div>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kelola Field — {{ $documentType->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">
            Template: <code class="bg-gray-100 px-1 rounded text-xs">{{ $documentType->template_filename }}</code>
            &nbsp;|&nbsp;
            Tipe: <span class="font-medium">{{ strtoupper($documentType->file_type) }}</span>
            &nbsp;|&nbsp;
            Key: <code class="bg-gray-100 px-1 rounded text-xs">{{ $documentType->key }}</code>
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.document-types.slots', $documentType) }}"
           class="text-sm px-4 py-2 rounded-lg border border-indigo-400 text-indigo-600 hover:bg-indigo-50">
            ⚙ Kelola Slot Autofill
        </a>
        <a href="{{ route('admin.document-types.reupload', $documentType) }}"
           class="text-sm px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">
            ↑ Re-upload Template
        </a>
    </div>
</div>

{{-- Slot summary --}}
@if ($slots->isNotEmpty())
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3 mb-5 text-sm text-indigo-800">
        <span class="font-semibold">Slot autofill aktif:</span>
        @foreach ($slots as $slot)
            <code class="bg-indigo-100 px-2 py-0.5 rounded text-xs mx-1">{{ $slot->slot_key }}</code> ({{ $slot->slot_label }})@if (!$loop->last),@endif
        @endforeach
        — gunakan nilai ini di kolom <em>Autofill Role</em> pada field yang ingin diisi otomatis.
    </div>
@else
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 mb-5 text-sm text-yellow-800">
        Belum ada slot autofill. <a href="{{ route('admin.document-types.slots', $documentType) }}" class="underline font-medium">Tambahkan slot</a> terlebih dahulu jika template ini membutuhkan fitur autofill.
    </div>
@endif

<div class="grid grid-cols-5 gap-6">

    <div class="col-span-2">
        <div class="bg-white rounded-lg shadow p-5 sticky top-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide border-b pb-2">
                Tambah Field Baru
            </h2>

            <form method="POST" action="{{ route('admin.document-types.fields.store', $documentType) }}">
                @csrf
                <div class="space-y-4">

                    {{-- Field Key --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Field Key <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="field_key" value="{{ old('field_key') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="employee_name" />
                        <p class="text-xs text-gray-400 mt-0.5">Hanya huruf kecil, angka, underscore. Harus cocok dengan placeholder di template.</p>
                        @error('field_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Label --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama Pegawai" />
                        @error('label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Field Type --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tipe Field <span class="text-red-500">*</span>
                        </label>
                        <select name="field_type" id="add-field-type"
                            onchange="onFieldTypeChange('add', this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach (\App\Models\DocumentField::fieldTypes() as $value => $label)
                                <option value="{{ $value }}" {{ old('field_type') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select options --}}
                    <div id="add-field-options" class="hidden">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Opsi (pisahkan dengan koma)</label>
                        <input type="text" name="field_options" value="{{ old('field_options') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Opsi A, Opsi B, Opsi C" />
                    </div>

                    {{-- Section Label --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Label Seksi (opsional)</label>
                        <input type="text" name="section_label" value="{{ old('section_label') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Data Pegawai" />
                        <p class="text-xs text-gray-400 mt-0.5">Tampilkan heading seksi di atas field ini.</p>
                    </div>

                    {{-- Row Group --}}
                    <div id="add-row-group-section">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Row Group (opsional)</label>
                        <input type="number" name="row_group" value="{{ old('row_group') }}" min="1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="1" />
                        <p class="text-xs text-gray-400 mt-0.5">Field dengan angka yang sama akan tampil berdampingan dalam satu baris.</p>
                    </div>

                    {{-- Staff Autofill Column (hidden for loop types) --}}
                    <div id="add-autofill-section">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Staff Autofill Column</label>
                        <select name="staff_autofill_column"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Tidak ada —</option>
                            @foreach (\App\Models\DocumentField::staffColumns() as $col => $colLabel)
                                <option value="{{ $col }}" {{ old('staff_autofill_column') === $col ? 'selected' : '' }}>
                                    {{ $colLabel }} ({{ $col }})
                                </option>
                            @endforeach
                        </select>

                        <label class="block text-xs font-medium text-gray-600 mb-1 mt-3">Autofill Role</label>
                        <input type="text" name="autofill_role" value="{{ old('autofill_role', 'none') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="none" />
                        <p class="text-xs text-gray-400 mt-0.5">
                            Isi dengan slot_key yang sesuai (misal: <code class="bg-gray-100 px-1 rounded">employee</code>),
                            atau <code class="bg-gray-100 px-1 rounded">none</code> jika tidak ada autofill.
                            @if ($slots->isNotEmpty())
                                Slot tersedia:
                                @foreach ($slots as $slot)
                                    <code class="bg-indigo-100 px-1 rounded cursor-pointer hover:bg-indigo-200"
                                          onclick="document.querySelector('[name=autofill_role]').value='{{ $slot->slot_key }}'">
                                        {{ $slot->slot_key }}
                                    </code>
                                @endforeach
                            @endif
                        </p>
                    </div>

                    {{-- Group child --}}
                    <div id="add-group-child-section" class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_group_child" id="add-is-group-child"
                                value="1" {{ old('is_group_child') ? 'checked' : '' }}
                                onchange="toggleGroupKey('add', this.checked)" />
                            <label for="add-is-group-child" class="text-xs font-medium text-gray-600">
                                Child dari Repeating Group
                            </label>
                        </div>
                        <div id="add-group-key-input" class="{{ old('is_group_child') ? '' : 'hidden' }}">
                            <input type="text" name="group_key" value="{{ old('group_key') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="group_key dari parent" />
                        </div>
                    </div>

                    {{-- Required --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_required" id="add-is-required"
                            value="1" {{ old('is_required') ? 'checked' : '' }} />
                        <label for="add-is-required" class="text-xs font-medium text-gray-600">Wajib diisi</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        + Tambah Field
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-span-3">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    Field Terdaftar ({{ $fields->count() }})
                </h2>
                <p class="text-xs text-gray-400">Drag untuk mengubah urutan</p>
            </div>

            @if ($fields->isEmpty())
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    Belum ada field. Tambahkan menggunakan form di sebelah kiri.
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-left text-xs uppercase">
                        <tr>
                            <th class="px-2 py-3 w-6"></th>
                            <th class="px-3 py-3">Key / Label</th>
                            <th class="px-3 py-3">Tipe</th>
                            <th class="px-3 py-3">Autofill</th>
                            <th class="px-3 py-3 text-center w-10">Row</th>
                            <th class="px-3 py-3 text-center w-10">Wajib</th>
                            <th class="px-3 py-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="fields-sortable" class="divide-y divide-gray-100">
                        @foreach ($fields as $field)
                            <tr data-id="{{ $field->id }}" class="hover:bg-gray-50">
                                <td class="px-2 py-3 text-gray-300 cursor-grab select-none text-base">⠿</td>
                                <td class="px-3 py-3">
                                    <span class="font-mono text-xs text-gray-400 block">{{ $field->field_key }}</span>
                                    <span class="text-gray-700 text-xs block">{{ $field->label }}</span>
                                    @if ($field->section_label)
                                        <span class="text-blue-400 text-xs block">§ {{ $field->section_label }}</span>
                                    @endif
                                    @if ($field->is_group_child)
                                        <span class="text-purple-400 text-xs block">↳ {{ $field->group_key }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <span class="px-1.5 py-0.5 rounded text-xs font-medium
                                        {{ in_array($field->field_type, ['staff_loop','official_loop']) ? 'bg-green-100 text-green-700' :
                                          ($field->field_type === 'repeating_group' ? 'bg-purple-100 text-purple-700' :
                                          ($field->field_type === 'select' ? 'bg-yellow-100 text-yellow-700' :
                                          'bg-gray-100 text-gray-600')) }}">
                                        {{ $field->field_type }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-xs">
                                    @if ($field->staff_autofill_column && $field->autofill_role !== 'none')
                                        <span class="block text-gray-500">{{ $field->staff_autofill_column }}</span>
                                        <code class="px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-600">
                                            {{ $field->autofill_role }}
                                        </code>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center text-xs text-gray-400">
                                    {{ $field->row_group ?? '—' }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if ($field->is_required)
                                        <span class="text-green-600 font-bold">✓</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-col gap-1">
                                        <button type="button"
                                            onclick="openEditField(
                                                {{ $field->id }},
                                                '{{ addslashes($field->label) }}',
                                                '{{ $field->field_type }}',
                                                '{{ addslashes(implode(', ', $field->field_options ?? [])) }}',
                                                {{ $field->is_required ? 'true' : 'false' }},
                                                '{{ addslashes($field->section_label ?? '') }}',
                                                '{{ $field->staff_autofill_column ?? '' }}',
                                                '{{ $field->autofill_role ?? 'none' }}',
                                                {{ $field->row_group ?? 'null' }}
                                            )"
                                            class="text-xs px-2 py-1 rounded border border-blue-400 text-blue-600 hover:bg-blue-50 text-center">
                                            Edit
                                        </button>
                                        <form method="POST"
                                              action="{{ route('admin.document-types.fields.destroy', [$documentType, $field]) }}"
                                              onsubmit="return confirm('Hapus field {{ addslashes($field->label) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full text-xs px-2 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50 text-center">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<div id="edit-field-modal"
     class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Edit Field</h2>
            <button onclick="closeEditField()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
        </div>

        <form method="POST" id="edit-field-form" action="">
            @csrf
            @method('PATCH')
            <div class="space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Label <span class="text-red-500">*</span></label>
                    <input type="text" name="label" id="edit-field-label"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Field</label>
                    <select name="field_type" id="edit-field-type"
                        onchange="onFieldTypeChange('edit', this.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach (\App\Models\DocumentField::fieldTypes() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="edit-field-options-wrap" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Opsi (pisahkan dengan koma)</label>
                    <input type="text" name="field_options" id="edit-field-options-input"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Label Seksi</label>
                    <input type="text" name="section_label" id="edit-field-section"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Row Group</label>
                    <input type="number" name="row_group" id="edit-field-row-group" min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="kosongkan jika full width" />
                </div>

                <div id="edit-autofill-section">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Staff Autofill Column</label>
                    <select name="staff_autofill_column" id="edit-field-autofill"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Tidak ada —</option>
                        @foreach (\App\Models\DocumentField::staffColumns() as $col => $colLabel)
                            <option value="{{ $col }}">{{ $colLabel }} ({{ $col }})</option>
                        @endforeach
                    </select>

                    <label class="block text-xs font-medium text-gray-600 mb-1 mt-3">Autofill Role</label>
                    <input type="text" name="autofill_role" id="edit-field-autofill-role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="none" />
                    @if ($slots->isNotEmpty())
                        <p class="text-xs text-gray-400 mt-0.5">
                            Slot tersedia:
                            @foreach ($slots as $slot)
                                <code class="bg-indigo-100 px-1 rounded cursor-pointer hover:bg-indigo-200"
                                      onclick="document.getElementById('edit-field-autofill-role').value='{{ $slot->slot_key }}'">
                                    {{ $slot->slot_key }}
                                </code>
                            @endforeach
                        </p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_required" id="edit-field-required" value="1" />
                    <label for="edit-field-required" class="text-xs font-medium text-gray-600">Wajib diisi</label>
                </div>

            </div>

            <div class="flex gap-3 mt-5 justify-end">
                <button type="button" onclick="closeEditField()"
                    class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const LOOP_TYPES = ['staff_loop', 'official_loop'];
const NO_AUTOFILL_TYPES = ['staff_loop', 'official_loop', 'repeating_group', 'checkbox'];
const NO_ROWGROUP_TYPES = ['staff_loop', 'official_loop', 'repeating_group'];

function onFieldTypeChange(prefix, type) {
    // Options input — only for select
    const optEl = document.getElementById(prefix === 'add' ? 'add-field-options' : 'edit-field-options-wrap');
    if (optEl) optEl.classList.toggle('hidden', type !== 'select');

    // Autofill section — hidden for loop/group types
    const autofillEl = document.getElementById(prefix === 'add' ? 'add-autofill-section' : 'edit-autofill-section');
    if (autofillEl) autofillEl.classList.toggle('hidden', NO_AUTOFILL_TYPES.includes(type));

    // Row group — hidden for loop/group types
    const rowGroupEl = document.getElementById(prefix === 'add' ? 'add-row-group-section' : null);
    if (rowGroupEl) rowGroupEl.classList.toggle('hidden', NO_ROWGROUP_TYPES.includes(type));

    // Group child section — only shown for regular fields
    const gcEl = document.getElementById('add-group-child-section');
    if (gcEl) gcEl.classList.toggle('hidden', LOOP_TYPES.includes(type) || type === 'repeating_group');
}

function toggleGroupKey(prefix, checked) {
    const el = document.getElementById(prefix + '-group-key-input');
    if (el) el.classList.toggle('hidden', !checked);
}

function openEditField(id, label, type, options, required, section, autofill, autofillRole, rowGroup) {
    document.getElementById('edit-field-form').action =
        `/admin/document-types/{{ $documentType->id }}/fields/${id}`;
    document.getElementById('edit-field-label').value          = label;
    document.getElementById('edit-field-section').value        = section;
    document.getElementById('edit-field-options-input').value  = options;
    document.getElementById('edit-field-required').checked     = required;
    document.getElementById('edit-field-autofill').value       = autofill || '';
    document.getElementById('edit-field-autofill-role').value  = autofillRole || 'none';
    document.getElementById('edit-field-row-group').value      = rowGroup || '';

    document.getElementById('edit-field-type').value = type;
    onFieldTypeChange('edit', type);

    document.getElementById('edit-field-modal').classList.remove('hidden');
}

function closeEditField() {
    document.getElementById('edit-field-modal').classList.add('hidden');
}

document.getElementById('edit-field-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditField();
});

(function() {
    const tbody = document.getElementById('fields-sortable');
    if (!tbody) return;
    let dragging = null;

    tbody.addEventListener('dragstart', function(e) {
        dragging = e.target.closest('tr');
        dragging.style.opacity = '0.5';
    });
    tbody.addEventListener('dragend', function() {
        dragging.style.opacity = '';
        dragging = null;
        saveOrder();
    });
    tbody.addEventListener('dragover', function(e) {
        e.preventDefault();
        const target = e.target.closest('tr');
        if (target && target !== dragging) {
            const rect = target.getBoundingClientRect();
            const next = (e.clientY - rect.top) > (rect.height / 2) ? target.nextSibling : target;
            tbody.insertBefore(dragging, next);
        }
    });
    tbody.querySelectorAll('tr').forEach(function(row) { row.setAttribute('draggable', true); });

    function saveOrder() {
        const order = Array.from(tbody.querySelectorAll('tr')).map(function(r) { return r.dataset.id; });
        fetch('{{ route('admin.document-types.fields.reorder', $documentType) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ order: order }),
        });
    }
})();
</script>

@endsection