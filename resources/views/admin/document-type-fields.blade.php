@extends('admin.layout')

@section('content')

    {{-- Toast notification --}}
    <div id="fields-toast"
         style="display:none;position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:9999;
                padding:0.55rem 1.1rem;border-radius:8px;font-size:0.82rem;font-weight:500;color:#fff;
                box-shadow:0 4px 16px rgba(0,0,0,0.22);transition:opacity 0.3s ease;white-space:nowrap;">
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-3 py-2 rounded text-xs mb-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        {{-- ── ADD FIELD FORM ────────────────────────────────────────── --}}
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow p-5 sticky top-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide border-b pb-2">
                    Tambah Field Baru
                </h2>

                <div class="space-y-4" id="add-field-form-wrapper">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Field Key <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="add-field_key"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="employee_name" />
                        <p class="text-xs text-gray-400 mt-0.5">Hanya huruf kecil, angka, underscore.</p>
                        <p class="text-xs text-red-500 mt-0.5 hidden" id="err-field_key"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="add-label"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama Pegawai" />
                        <p class="text-xs text-red-500 mt-0.5 hidden" id="err-label"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tipe Field <span class="text-red-500">*</span>
                        </label>
                        <select id="add-field_type"
                            onchange="onFieldTypeChange('add', this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach (\App\Models\DocumentField::fieldTypes() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="add-field-options" class="hidden">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Opsi (pisahkan dengan koma)</label>
                        <input type="text" id="add-field_options"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Opsi A, Opsi B, Opsi C" />
                    </div>

                    {{-- Icon picker --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Icon (opsional)</label>
                        <input type="hidden" id="add-icon" value="" />
                        <div class="flex items-center gap-2 mb-2">
                            <div id="add-icon-preview"
                                 class="w-8 h-8 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400 text-sm">
                                <span class="text-gray-300 text-xs">—</span>
                            </div>
                            <span id="add-icon-label" class="text-xs text-gray-500 flex-1">Belum dipilih</span>
                            <button type="button" onclick="clearIcon('add')"
                                class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded border border-red-200 hover:border-red-400">
                                Hapus
                            </button>
                        </div>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-2 py-1.5 border-b bg-gray-50">
                                <input type="text" placeholder="Cari icon..."
                                    class="w-full text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-400"
                                    oninput="filterIcons('add', this.value)" />
                            </div>
                            <div id="add-icon-grid" class="overflow-y-auto p-2" style="max-height:200px;">
                                @foreach (\App\Models\DocumentField::availableIcons() as $groupName => $icons)
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-2 mb-1 first:mt-0" data-group-label>{{ $groupName }}</p>
                                    <div class="grid grid-cols-8 gap-1 mb-1">
                                        @foreach ($icons as $faClass => $iconLabel)
                                            <button type="button" title="{{ $iconLabel }}"
                                                data-icon="{{ $faClass }}" data-label="{{ $iconLabel }}"
                                                onclick="selectIcon('add', '{{ $faClass }}', '{{ $iconLabel }}')"
                                                class="icon-btn w-7 h-7 rounded flex items-center justify-center text-sm text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition border border-transparent hover:border-blue-200">
                                                <i class="{{ $faClass }}"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Label Seksi (opsional)</label>
                        <input type="text" id="add-section_label"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Data Pegawai" />
                    </div>

                    <div id="add-row-group-section">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Row Group (opsional)</label>
                        <input type="number" id="add-row_group" min="1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="1" />
                        <p class="text-xs text-gray-400 mt-0.5">Field dengan angka yang sama akan tampil berdampingan.</p>
                    </div>

                    <div id="add-autofill-section">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Staff Autofill Column</label>
                        <select id="add-staff_autofill_column"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Tidak ada —</option>
                            @foreach (\App\Models\DocumentField::staffColumns() as $col => $colLabel)
                                <option value="{{ $col }}">{{ $colLabel }} ({{ $col }})</option>
                            @endforeach
                        </select>

                        <label class="block text-xs font-medium text-gray-600 mb-1 mt-3">Autofill Role</label>
                        <input type="text" id="add-autofill_role" value="none"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="none" />
                        <p class="text-xs text-gray-400 mt-0.5">
                            @if ($slots->isNotEmpty())
                                Slot tersedia:
                                @foreach ($slots as $slot)
                                    <code class="bg-indigo-100 px-1 rounded cursor-pointer hover:bg-indigo-200"
                                          onclick="document.getElementById('add-autofill_role').value='{{ $slot->slot_key }}'">
                                        {{ $slot->slot_key }}
                                    </code>
                                @endforeach
                            @endif
                        </p>
                    </div>

                    <div id="add-group-child-section" class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="add-is_group_child" value="1"
                                onchange="toggleGroupKey('add', this.checked)" />
                            <label for="add-is_group_child" class="text-xs font-medium text-gray-600">
                                Child dari Repeating Group
                            </label>
                        </div>
                        <div id="add-group-key-input" class="hidden">
                            <input type="text" id="add-group_key"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="group_key dari parent" />
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="add-is_required" value="1" />
                        <label for="add-is_required" class="text-xs font-medium text-gray-600">Wajib diisi</label>
                    </div>

                    <button type="button" onclick="submitAddField()"
                        id="add-submit-btn"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        + Tambah Field
                    </button>
                </div>
            </div>
        </div>

        {{-- ── FIELDS TABLE ──────────────────────────────────────────── --}}
        <div class="col-span-3">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide" id="fields-count-heading">
                        Field Terdaftar ({{ $fields->count() }})
                    </h2>
                    <p class="text-xs text-gray-400">Drag untuk mengubah urutan</p>
                </div>

                @if ($fields->isEmpty())
                    <div class="px-5 py-10 text-center text-gray-400 text-sm" id="empty-state">
                        Belum ada field. Tambahkan menggunakan form di sebelah kiri.
                    </div>
                @endif

                <table class="w-full text-sm" id="fields-table" {{ $fields->isEmpty() ? 'style=display:none' : '' }}>
                    <thead class="bg-gray-50 text-gray-500 text-left text-xs uppercase">
                        <tr>
                            <th class="px-2 py-3 w-6"></th>
                            <th class="px-3 py-3">Icon</th>
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
                            @include('admin.partials.field-row', ['field' => $field, 'documentType' => $documentType, 'slots' => $slots])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── EDIT MODAL ────────────────────────────────────────────────── --}}
    <div id="edit-field-modal"
         class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Edit Field</h2>
                <button onclick="closeEditField()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </div>

            <div class="space-y-4">
                <input type="hidden" id="edit-field-id" value="">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Label <span class="text-red-500">*</span></label>
                    <input type="text" id="edit-field-label"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Field</label>
                    <select id="edit-field-type"
                        onchange="onFieldTypeChange('edit', this.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach (\App\Models\DocumentField::fieldTypes() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="edit-field-options-wrap" class="hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Opsi (pisahkan dengan koma)</label>
                    <input type="text" id="edit-field-options-input"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                {{-- Edit icon picker --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Icon (opsional)</label>
                    <input type="hidden" id="edit-icon-value" value="" />
                    <div class="flex items-center gap-2 mb-2">
                        <div id="edit-icon-preview"
                             class="w-8 h-8 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400 text-sm">
                            <span class="text-gray-300 text-xs">—</span>
                        </div>
                        <span id="edit-icon-label" class="text-xs text-gray-500 flex-1">Belum dipilih</span>
                        <button type="button" onclick="clearIcon('edit')"
                            class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded border border-red-200 hover:border-red-400">
                            Hapus
                        </button>
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-2 py-1.5 border-b bg-gray-50">
                            <input type="text" placeholder="Cari icon..."
                                class="w-full text-xs border border-gray-200 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-400"
                                oninput="filterIcons('edit', this.value)" />
                        </div>
                        <div id="edit-icon-grid" class="overflow-y-auto p-2" style="max-height:200px;">
                            @foreach (\App\Models\DocumentField::availableIcons() as $groupName => $icons)
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-2 mb-1 first:mt-0" data-group-label>{{ $groupName }}</p>
                                <div class="grid grid-cols-8 gap-1 mb-1">
                                    @foreach ($icons as $faClass => $iconLabel)
                                        <button type="button" title="{{ $iconLabel }}"
                                            data-icon="{{ $faClass }}" data-label="{{ $iconLabel }}"
                                            onclick="selectIcon('edit', '{{ $faClass }}', '{{ $iconLabel }}')"
                                            class="icon-btn w-7 h-7 rounded flex items-center justify-center text-sm text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition border border-transparent hover:border-blue-200">
                                            <i class="{{ $faClass }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Label Seksi</label>
                    <input type="text" id="edit-field-section"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Row Group</label>
                    <input type="number" id="edit-field-row-group" min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="kosongkan jika full width" />
                </div>

                <div id="edit-autofill-section">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Staff Autofill Column</label>
                    <select id="edit-field-autofill"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Tidak ada —</option>
                        @foreach (\App\Models\DocumentField::staffColumns() as $col => $colLabel)
                            <option value="{{ $col }}">{{ $colLabel }} ({{ $col }})</option>
                        @endforeach
                    </select>

                    <label class="block text-xs font-medium text-gray-600 mb-1 mt-3">Autofill Role</label>
                    <input type="text" id="edit-field-autofill-role"
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
                    <input type="checkbox" id="edit-field-required" value="1" />
                    <label for="edit-field-required" class="text-xs font-medium text-gray-600">Wajib diisi</label>
                </div>
            </div>

            <div class="flex gap-3 mt-5 justify-end">
                <button type="button" onclick="closeEditField()"
                    class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="button" onclick="submitEditField()" id="edit-submit-btn"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <script>
    const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
    const DOC_TYPE_ID = {{ $documentType->id }};
    const STORE_URL   = '{{ route('admin.document-types.fields.store', $documentType) }}';
    const UPDATE_BASE = '{{ url('/admin/document-types/' . $documentType->id . '/fields') }}';
    const REORDER_URL = '{{ route('admin.document-types.fields.reorder', $documentType) }}';

    const LOOP_TYPES        = ['staff_loop', 'official_loop'];
    const NO_AUTOFILL_TYPES = ['staff_loop', 'official_loop', 'repeating_group', 'checkbox'];
    const NO_ROWGROUP_TYPES = ['staff_loop', 'official_loop', 'repeating_group'];

    // ── Toast ─────────────────────────────────────────────────────────
    function showToast(msg, ok = true) {
        const t = document.getElementById('fields-toast');
        t.textContent = msg;
        t.style.background = ok ? '#1e3058' : '#b91c1c';
        t.style.display = 'block';
        t.style.opacity = '1';
        clearTimeout(t._timer);
        t._timer = setTimeout(function() { t.style.opacity = '0'; setTimeout(function() { t.style.display = 'none'; }, 300); }, 2500);
    }

    // ── Field type change visibility ──────────────────────────────────
    function onFieldTypeChange(prefix, type) {
        const optEl = document.getElementById(prefix === 'add' ? 'add-field-options' : 'edit-field-options-wrap');
        if (optEl) optEl.classList.toggle('hidden', type !== 'select');

        const autofillEl = document.getElementById(prefix === 'add' ? 'add-autofill-section' : 'edit-autofill-section');
        if (autofillEl) autofillEl.classList.toggle('hidden', NO_AUTOFILL_TYPES.includes(type));

        const rowGroupEl = document.getElementById(prefix === 'add' ? 'add-row-group-section' : null);
        if (rowGroupEl) rowGroupEl.classList.toggle('hidden', NO_ROWGROUP_TYPES.includes(type));

        const gcEl = document.getElementById('add-group-child-section');
        if (gcEl) gcEl.classList.toggle('hidden', LOOP_TYPES.includes(type) || type === 'repeating_group');
    }

    function toggleGroupKey(prefix, checked) {
        const el = document.getElementById(prefix + '-group-key-input');
        if (el) el.classList.toggle('hidden', !checked);
    }

    // ── Icon helpers ──────────────────────────────────────────────────
    function selectIcon(prefix, faClass, label) {
        document.getElementById(prefix === 'add' ? 'add-icon' : 'edit-icon-value').value = faClass;
        document.getElementById(prefix + '-icon-label').textContent = label + ' (' + faClass + ')';
        const preview = document.getElementById(prefix + '-icon-preview');
        preview.innerHTML = '<i class="' + faClass + ' text-blue-500"></i>';
        document.querySelectorAll('#' + prefix + '-icon-grid .icon-btn').forEach(function(btn) {
            const sel = btn.dataset.icon === faClass;
            btn.classList.toggle('bg-blue-100', sel);
            btn.classList.toggle('text-blue-600', sel);
            btn.classList.toggle('border-blue-300', sel);
            btn.classList.toggle('text-gray-500', !sel);
        });
    }

    function clearIcon(prefix) {
        document.getElementById(prefix === 'add' ? 'add-icon' : 'edit-icon-value').value = '';
        document.getElementById(prefix + '-icon-label').textContent = 'Belum dipilih';
        document.getElementById(prefix + '-icon-preview').innerHTML = '<span class="text-gray-300 text-xs">—</span>';
        document.querySelectorAll('#' + prefix + '-icon-grid .icon-btn').forEach(function(btn) {
            btn.classList.remove('bg-blue-100', 'text-blue-600', 'border-blue-300');
            btn.classList.add('text-gray-500');
        });
    }

    function filterIcons(prefix, query) {
        const grid = document.getElementById(prefix + '-icon-grid');
        const q = query.toLowerCase().trim();
        grid.querySelectorAll('.icon-btn').forEach(function(btn) {
            btn.style.display = (!q || btn.dataset.label.toLowerCase().includes(q) || btn.dataset.icon.toLowerCase().includes(q)) ? '' : 'none';
        });
        grid.querySelectorAll('[data-group-label]').forEach(function(label) {
            const visible = label.nextElementSibling
                ? label.nextElementSibling.querySelectorAll('.icon-btn:not([style*="none"])').length
                : 0;
            label.style.display = visible === 0 && q ? 'none' : '';
        });
    }

    // ── Add field ─────────────────────────────────────────────────────
    async function submitAddField() {
        const btn = document.getElementById('add-submit-btn');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const body = {
            field_key:             document.getElementById('add-field_key').value.trim(),
            label:                 document.getElementById('add-label').value.trim(),
            field_type:            document.getElementById('add-field_type').value,
            field_options:         document.getElementById('add-field_options').value.trim(),
            section_label:         document.getElementById('add-section_label').value.trim(),
            row_group:             document.getElementById('add-row_group').value.trim(),
            staff_autofill_column: document.getElementById('add-staff_autofill_column').value,
            autofill_role:         document.getElementById('add-autofill_role').value.trim() || 'none',
            is_required:           document.getElementById('add-is_required').checked ? '1' : '0',
            is_group_child:        document.getElementById('add-is_group_child').checked ? '1' : '0',
            group_key:             document.getElementById('add-group_key') ? document.getElementById('add-group_key').value.trim() : '',
            icon:                  document.getElementById('add-icon').value,
        };

        try {
            const res = await fetch(STORE_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();

            if (!res.ok) {
                handleValidationErrors(data.errors || {});
                showToast('Periksa kembali isian form.', false);
                return;
            }

            clearValidationErrors();
            appendFieldRow(data.field);
            resetAddForm();
            updateFieldCount(1);
            showToast("Field '" + data.field.label + "' berhasil ditambahkan.");
        } catch (e) {
            showToast('Terjadi kesalahan. Coba lagi.', false);
        } finally {
            btn.disabled = false;
            btn.textContent = '+ Tambah Field';
        }
    }

    function handleValidationErrors(errors) {
        clearValidationErrors();
        Object.entries(errors).forEach(function([field, messages]) {
            const el = document.getElementById('err-' + field);
            if (el) { el.textContent = messages[0]; el.classList.remove('hidden'); }
        });
    }

    function clearValidationErrors() {
        document.querySelectorAll('[id^="err-"]').forEach(function(el) { el.classList.add('hidden'); el.textContent = ''; });
    }

    function resetAddForm() {
        ['add-field_key', 'add-label', 'add-field_options', 'add-section_label', 'add-row_group',
         'add-group_key'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        document.getElementById('add-autofill_role').value = 'none';
        document.getElementById('add-staff_autofill_column').value = '';
        document.getElementById('add-is_required').checked = false;
        document.getElementById('add-is_group_child').checked = false;
        document.getElementById('add-field_type').value = 'text';
        onFieldTypeChange('add', 'text');
        clearIcon('add');
        const gkInput = document.getElementById('add-group-key-input');
        if (gkInput) gkInput.classList.add('hidden');
    }

    // ── Build a table row from field data ─────────────────────────────
    function typeClass(type) {
        if (['staff_loop','official_loop'].includes(type)) return 'bg-green-100 text-green-700';
        if (type === 'repeating_group')                    return 'bg-purple-100 text-purple-700';
        if (type === 'select')                             return 'bg-yellow-100 text-yellow-700';
        return 'bg-gray-100 text-gray-600';
    }

    function appendFieldRow(field) {
        const tbody = document.getElementById('fields-sortable');
        const emptyState = document.getElementById('empty-state');
        const table      = document.getElementById('fields-table');
        if (emptyState) emptyState.style.display = 'none';
        if (table)      table.style.display = '';

        const tr = document.createElement('tr');
        tr.setAttribute('data-id', field.id);
        tr.className = 'hover:bg-gray-50';
        tr.setAttribute('draggable', true);
        tr.innerHTML = buildRowHTML(field);
        tbody.appendChild(tr);

        tr.style.transition = 'background 0.5s';
        tr.style.background = '#eff6ff';
        setTimeout(function() { tr.style.background = ''; }, 900);
    }

    function buildRowHTML(f) {
        const iconCell = f.icon
            ? '<span class="inline-flex items-center justify-center w-7 h-7 rounded bg-blue-50 text-blue-500 text-sm"><i class="' + f.icon + '"></i></span>'
            : '<span class="text-gray-300 text-xs">—</span>';

        const autofillCell = (f.staff_autofill_column && f.autofill_role && f.autofill_role !== 'none')
            ? '<span class="block text-gray-500">' + f.staff_autofill_column + '</span><code class="px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-600">' + f.autofill_role + '</code>'
            : '<span class="text-gray-300">—</span>';

        const sectionPart  = f.section_label ? '<span class="text-blue-400 text-xs block">§ ' + f.section_label + '</span>' : '';
        const groupPart    = f.is_group_child ? '<span class="text-purple-400 text-xs block">↳ ' + (f.group_key || '') + '</span>' : '';

        const optionsStr = Array.isArray(f.field_options) ? f.field_options.join(', ') : (f.field_options || '');

        return `
            <td class="px-2 py-3 text-gray-300 cursor-grab select-none text-base">⠿</td>
            <td class="px-3 py-3 text-center">${iconCell}</td>
            <td class="px-3 py-3">
                <span class="font-mono text-xs text-gray-400 block">${e(f.field_key)}</span>
                <span class="text-gray-700 text-xs block">${e(f.label)}</span>
                ${sectionPart}${groupPart}
            </td>
            <td class="px-3 py-3">
                <span class="px-1.5 py-0.5 rounded text-xs font-medium ${typeClass(f.field_type)}">${e(f.field_type)}</span>
            </td>
            <td class="px-3 py-3 text-xs">${autofillCell}</td>
            <td class="px-3 py-3 text-center text-xs text-gray-400">${f.row_group !== null && f.row_group !== undefined ? f.row_group : '—'}</td>
            <td class="px-3 py-3 text-center">${f.is_required ? '<span class="text-green-600 font-bold">✓</span>' : '<span class="text-gray-300">—</span>'}</td>
            <td class="px-3 py-3">
                <div class="flex flex-col gap-1">
                    <button type="button"
                        onclick="openEditField(${f.id})"
                        class="text-xs px-2 py-1 rounded border border-blue-400 text-blue-600 hover:bg-blue-50 text-center">
                        Edit
                    </button>
                    <button type="button"
                        onclick="deleteField(${f.id}, '${esc(f.label)}')"
                        class="text-xs px-2 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50 text-center">
                        Hapus
                    </button>
                </div>
            </td>`;
    }

    function e(str) { return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function esc(str) { return String(str ?? '').replace(/'/g,"\\'"); }

    // ── Field cache for the edit modal ────────────────────────────────
    const fieldCache = {};

    @foreach ($fields as $field)
    fieldCache[{{ $field->id }}] = {
        id: {{ $field->id }},
        label: {{ json_encode($field->label) }},
        field_type: {{ json_encode($field->field_type) }},
        field_options: {{ json_encode(implode(', ', $field->field_options ?? [])) }},
        is_required: {{ $field->is_required ? 'true' : 'false' }},
        section_label: {{ json_encode($field->section_label ?? '') }},
        staff_autofill_column: {{ json_encode($field->staff_autofill_column ?? '') }},
        autofill_role: {{ json_encode($field->autofill_role ?? 'none') }},
        row_group: {{ $field->row_group ?? 'null' }},
        icon: {{ json_encode($field->icon ?? '') }},
    };
    @endforeach

    function openEditField(id) {
        const f = fieldCache[id];
        if (!f) { showToast('Data field tidak ditemukan.', false); return; }

        document.getElementById('edit-field-id').value          = f.id;
        document.getElementById('edit-field-label').value       = f.label;
        document.getElementById('edit-field-section').value     = f.section_label;
        document.getElementById('edit-field-options-input').value = f.field_options;
        document.getElementById('edit-field-required').checked  = f.is_required;
        document.getElementById('edit-field-autofill').value    = f.staff_autofill_column;
        document.getElementById('edit-field-autofill-role').value = f.autofill_role;
        document.getElementById('edit-field-row-group').value   = f.row_group !== null ? f.row_group : '';
        document.getElementById('edit-field-type').value        = f.field_type;
        onFieldTypeChange('edit', f.field_type);

        if (f.icon) {
            const btn = document.querySelector('#edit-icon-grid .icon-btn[data-icon="' + f.icon + '"]');
            selectIcon('edit', f.icon, btn ? btn.dataset.label : f.icon);
        } else {
            clearIcon('edit');
        }

        document.getElementById('edit-field-modal').classList.remove('hidden');
    }

    function closeEditField() {
        document.getElementById('edit-field-modal').classList.add('hidden');
    }

    document.getElementById('edit-field-modal').addEventListener('click', function(e) {
        if (e.target === this) closeEditField();
    });

    async function submitEditField() {
        const id  = document.getElementById('edit-field-id').value;
        const btn = document.getElementById('edit-submit-btn');
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';

        const body = {
            label:                 document.getElementById('edit-field-label').value.trim(),
            field_type:            document.getElementById('edit-field-type').value,
            field_options:         document.getElementById('edit-field-options-input').value.trim(),
            section_label:         document.getElementById('edit-field-section').value.trim(),
            row_group:             document.getElementById('edit-field-row-group').value.trim(),
            staff_autofill_column: document.getElementById('edit-field-autofill').value,
            autofill_role:         document.getElementById('edit-field-autofill-role').value.trim() || 'none',
            is_required:           document.getElementById('edit-field-required').checked ? '1' : '0',
            icon:                  document.getElementById('edit-icon-value').value,
            _method:               'PATCH',
        };

        try {
            const res = await fetch(UPDATE_BASE + '/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();

            if (!res.ok) {
                showToast(data.message || 'Gagal menyimpan.', false);
                return;
            }

            // Update cache
            const f = data.field;
            fieldCache[f.id] = f;

            // Update the row in DOM
            const tr = document.querySelector('#fields-sortable tr[data-id="' + f.id + '"]');
            if (tr) {
                tr.innerHTML = buildRowHTML(f);
                tr.setAttribute('draggable', true);
                tr.style.transition = 'background 0.5s';
                tr.style.background = '#eff6ff';
                setTimeout(function() { tr.style.background = ''; }, 900);
            }

            closeEditField();
            showToast("Field '" + f.label + "' berhasil diperbarui.");
        } catch (ex) {
            showToast('Terjadi kesalahan. Coba lagi.', false);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Simpan';
        }
    }

    // ── Delete field ──────────────────────────────────────────────────
    async function deleteField(id, label) {
        if (!confirm('Hapus field "' + label + '"?')) return;

        try {
            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('_method', 'DELETE');
            const res = await fetch(UPDATE_BASE + '/' + id, { method: 'POST', body: fd });
            if (!res.ok) throw new Error();

            const tr = document.querySelector('#fields-sortable tr[data-id="' + id + '"]');
            if (tr) {
                tr.style.transition = 'opacity 0.3s';
                tr.style.opacity = '0';
                setTimeout(function() {
                    tr.remove();
                    updateFieldCount(-1);
                    if (!document.querySelector('#fields-sortable tr')) {
                        document.getElementById('fields-table').style.display = 'none';
                        const es = document.getElementById('empty-state');
                        if (es) es.style.display = '';
                    }
                }, 310);
            }

            delete fieldCache[id];
            showToast("Field '" + label + "' berhasil dihapus.");
        } catch (e) {
            showToast('Gagal menghapus. Coba lagi.', false);
        }
    }

    // ── Count heading ─────────────────────────────────────────────────
    function updateFieldCount(delta) {
        const h = document.getElementById('fields-count-heading');
        if (!h) return;
        const m = h.textContent.match(/\d+/);
        if (m) h.textContent = 'Field Terdaftar (' + (parseInt(m[0]) + delta) + ')';
    }

    // ── Drag-to-reorder ───────────────────────────────────────────────
    (function() {
        const tbody = document.getElementById('fields-sortable');
        if (!tbody) return;
        let dragging = null;

        tbody.addEventListener('dragstart', function(e) {
            dragging = e.target.closest('tr');
            if (dragging) dragging.style.opacity = '0.5';
        });
        tbody.addEventListener('dragend', function() {
            if (dragging) dragging.style.opacity = '';
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
            fetch(REORDER_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ order: order }),
            });
        }
    })();
    </script>

@endsection