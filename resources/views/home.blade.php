<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Automatisasi Surat — DINAS PUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    {{-- Navbar --}}
    <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
        <span class="font-bold text-gray-800">DINAS PUPRD Kota Tomohon</span>
        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                    <span class="ml-1 px-2 py-0.5 rounded text-xs font-semibold
                        {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ auth()->user()->role === 'staff' ? 'bg-blue-100 text-blue-700'   : '' }}
                        {{ auth()->user()->role === 'guest' ? 'bg-gray-100 text-gray-600'   : '' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </span>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-purple-600 hover:underline font-medium">
                        Admin Panel
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:underline">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"    class="text-sm text-blue-600 hover:underline">Login</a>
                <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">Daftar</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Sistem Automatisasi Surat</h1>
            <p class="text-sm text-gray-500 mt-1">Pilih jenis dokumen dan isi form yang tersedia.</p>
        </div>

        {{-- Flash messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span class="text-sm">{{ session('success') }}</span>
                @if (session('download_url'))
                    <a href="{{ session('download_url') }}"
                       class="ml-4 bg-green-600 text-white text-sm px-4 py-1.5 rounded hover:bg-green-700 font-medium whitespace-nowrap">
                        ⬇ Unduh Dokumen
                    </a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @auth
            @if (auth()->user()->isGuest())
                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">
                    Anda login sebagai <strong>Guest</strong>. Hanya dokumen publik yang tersedia.
                    Hubungi administrator untuk mendapatkan akses Staff.
                </div>
            @endif
        @endauth

        @if ($documentTypes->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
                Tidak ada dokumen yang tersedia saat ini.
            </div>
        @else

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('document.generate') }}" method="POST" id="main-form">
                @csrf

                {{-- Document type selector --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat / Dokumen</label>
                    <select name="letter-type" id="letter-type-select"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="showForm(this.value)">
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->key }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ============================================================ --}}
                {{-- Dynamic form sections                                        --}}
                {{-- ============================================================ --}}
                @foreach ($documentTypes as $docType)
                    @php
                        $fields      = $allFields[$docType->id] ?? collect();
                        $topFields   = $fields->where('is_group_child', false);
                        $autofillRole = $docType->staff_autofill_role;

                        // Determine which roles need autofill selectors
                        $needsEmployeeSelector  = in_array($autofillRole, ['employee', 'both']);
                        $needsAppraiserSelector = in_array($autofillRole, ['appraiser', 'both']);
                    @endphp

                    <div id="form-{{ $docType->key }}" class="{{ !$loop->first ? 'hidden' : '' }}">

                        {{-- -------------------------------------------------- --}}
                        {{-- Employee autofill selectors (Option A: staff + official) --}}
                        {{-- -------------------------------------------------- --}}
                        @if ($needsEmployeeSelector)
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm font-medium text-blue-700 mb-2">
                                    Pilih Pegawai (opsional — mengisi otomatis)
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-blue-600 mb-1">Dari Data Staff</label>
                                        <select onchange="fillFromSource('{{ $docType->key }}', 'employee', 'staff', this.value)"
                                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 staff-dropdown">
                                            <option value="">— Pilih Staff —</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-blue-600 mb-1">Dari Data Pejabat</label>
                                        <select onchange="fillFromSource('{{ $docType->key }}', 'employee', 'official', this.value)"
                                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 official-dropdown">
                                            <option value="">— Pilih Pejabat —</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- -------------------------------------------------- --}}
                        {{-- Appraiser autofill selectors (Option A: staff + official) --}}
                        {{-- -------------------------------------------------- --}}
                        @if ($needsAppraiserSelector)
                            <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <p class="text-sm font-medium text-indigo-700 mb-2">
                                    Pilih Penilai / Pejabat (opsional — mengisi otomatis)
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-indigo-600 mb-1">Dari Data Staff</label>
                                        <select onchange="fillFromSource('{{ $docType->key }}', 'appraiser', 'staff', this.value)"
                                            class="w-full border border-indigo-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 staff-dropdown">
                                            <option value="">— Pilih Staff —</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-indigo-600 mb-1">Dari Data Pejabat</label>
                                        <select onchange="fillFromSource('{{ $docType->key }}', 'appraiser', 'official', this.value)"
                                            class="w-full border border-indigo-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 official-dropdown">
                                            <option value="">— Pilih Pejabat —</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- -------------------------------------------------- --}}
                        {{-- Render fields                                        --}}
                        {{-- -------------------------------------------------- --}}
                        @php $currentSection = null; @endphp

                        @foreach ($topFields as $field)

                            @if ($field->section_label && $field->section_label !== $currentSection)
                                @php $currentSection = $field->section_label; @endphp
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-5 mb-3 border-b pb-2">
                                    {{ $field->section_label }}
                                </h3>
                            @endif

                            @if ($field->field_type === 'repeating_group')
                                @php
                                    $children = $fields->where('is_group_child', true)->where('group_key', $field->field_key);
                                @endphp
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $field->label }}
                                            @if ($field->is_required)<span class="text-red-500">*</span>@endif
                                        </label>
                                        <button type="button"
                                            onclick="addRow('{{ $docType->key }}', '{{ $field->field_key }}')"
                                            class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                            + Tambah Baris
                                        </button>
                                    </div>
                                    <div class="grid gap-2 mb-1"
                                         style="grid-template-columns: repeat({{ $children->count() }}, 1fr) auto">
                                        @foreach ($children as $child)
                                            <span class="text-xs font-medium text-gray-500">{{ $child->label }}</span>
                                        @endforeach
                                        <span></span>
                                    </div>
                                    <div id="rows-{{ $docType->key }}-{{ $field->field_key }}"></div>
                                    <template id="row-template-{{ $docType->key }}-{{ $field->field_key }}">
                                        <div class="grid gap-2 mb-2 row-item"
                                             style="grid-template-columns: repeat({{ $children->count() }}, 1fr) auto">
                                            @foreach ($children as $child)
                                                <input type="text"
                                                    name="field_{{ $field->field_key }}[__INDEX__][{{ $child->field_key }}]"
                                                    placeholder="{{ $child->label }}"
                                                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    {{ $child->is_required ? 'required' : '' }} />
                                            @endforeach
                                            <button type="button" onclick="removeRow(this)"
                                                class="text-red-400 hover:text-red-600 text-lg font-bold px-2">×</button>
                                        </div>
                                    </template>
                                </div>

                            @else
                                <div class="mb-4"
                                     data-doctype="{{ $docType->key }}"
                                     data-field-key="{{ $field->field_key }}"
                                     data-autofill-col="{{ $field->staff_autofill_column ?? '' }}"
                                     data-autofill-role="{{ $field->autofill_role }}">

                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $field->label }}
                                        @if ($field->is_required)<span class="text-red-500">*</span>@endif
                                    </label>

                                    @php
                                        $inputClass = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
                                        $inputName  = "field_{$field->field_key}";
                                        $oldValue   = old($inputName, '');
                                        $required   = $field->is_required ? 'required' : '';
                                    @endphp

                                    @if ($field->field_type === 'textarea')
                                        <textarea name="{{ $inputName }}" rows="3"
                                            class="{{ $inputClass }}" {{ $required }}>{{ $oldValue }}</textarea>

                                    @elseif ($field->field_type === 'date')
                                        <input type="date" name="{{ $inputName }}"
                                            value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }} />

                                    @elseif ($field->field_type === 'number')
                                        <input type="number" name="{{ $inputName }}"
                                            value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }} />

                                    @elseif ($field->field_type === 'select')
                                        <select name="{{ $inputName }}" class="{{ $inputClass }}" {{ $required }}>
                                            <option value="">— Pilih —</option>
                                            @foreach ($field->field_options ?? [] as $option)
                                                <option value="{{ $option }}" {{ $oldValue === $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @elseif ($field->field_type === 'checkbox')
                                        <div class="flex items-center gap-2 mt-1">
                                            <input type="checkbox" name="{{ $inputName }}"
                                                value="1" id="field-cb-{{ $field->field_key }}-{{ $docType->key }}"
                                                {{ $oldValue ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600" />
                                            <label for="field-cb-{{ $field->field_key }}-{{ $docType->key }}"
                                                class="text-sm text-gray-600">{{ $field->label }}</label>
                                        </div>

                                    @else
                                        <input type="text" name="{{ $inputName }}"
                                            value="{{ $oldValue }}" class="{{ $inputClass }}" {{ $required }}
                                            placeholder="{{ $field->label }}..." />
                                    @endif

                                </div>
                            @endif

                        @endforeach
                    </div>
                @endforeach

                {{-- Consent + Submit --}}
                <div class="mt-6 pt-4 border-t flex items-center gap-3">
                    <input type="checkbox" name="consent" id="consent"
                        class="rounded border-gray-300 text-blue-600" />
                    <label for="consent" class="text-sm text-gray-600">
                        Saya menyatakan bahwa informasi yang saya berikan adalah benar adanya.
                    </label>
                </div>

                <button type="button" onclick="submitIfConsented()"
                    class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Buat Dokumen
                </button>

            </form>
        </div>

        @endif
    </main>

    {{-- ============================================================ --}}
    {{-- Autofill map: { docTypeKey: { fieldKey: { col, role } } }    --}}
    {{-- ============================================================ --}}
    <script>
    const autofillMap = {
        @foreach ($documentTypes as $docType)
        "{{ $docType->key }}": {
            @foreach (($allFields[$docType->id] ?? collect())->whereNotNull('staff_autofill_column')->where('autofill_role', '!=', 'none') as $f)
            "{{ $f->field_key }}": { col: "{{ $f->staff_autofill_column }}", role: "{{ $f->autofill_role }}" },
            @endforeach
        },
        @endforeach
    };

    // ----------------------------------------------------------------
    // Data sources
    // ----------------------------------------------------------------
    let staffData    = [];
    let officialData = [];

    async function loadAllData() {
        try {
            const [staffRes, officialRes] = await Promise.all([
                fetch('{{ route('api.staff') }}'),
                fetch('{{ route('api.officials') }}'),
            ]);
            staffData    = await staffRes.json();
            officialData = await officialRes.json();
            populateDropdowns();
        } catch(e) {
            console.warn('Could not load autofill data:', e);
        }
    }

    function populateDropdowns() {
        // Staff dropdowns
        document.querySelectorAll('.staff-dropdown').forEach(function(select) {
            const ph = select.options[0];
            select.innerHTML = '';
            select.appendChild(ph);
            staffData.forEach(function(person) {
                const opt = document.createElement('option');
                opt.value = person.id;
                opt.textContent = person.staff_name + (person.nip ? ' — ' + person.nip : '');
                select.appendChild(opt);
            });
        });

        // Official dropdowns
        document.querySelectorAll('.official-dropdown').forEach(function(select) {
            const ph = select.options[0];
            select.innerHTML = '';
            select.appendChild(ph);
            officialData.forEach(function(person) {
                const opt = document.createElement('option');
                opt.value = person.id;
                opt.textContent = person.staff_name + (person.nip ? ' — ' + person.nip : '');
                select.appendChild(opt);
            });
        });
    }

    // ----------------------------------------------------------------
    // Fill form from a specific source and role
    // source = 'staff' | 'official'
    // autofillRole = 'employee' | 'appraiser'
    // ----------------------------------------------------------------
    function fillFromSource(docTypeKey, autofillRole, source, personId) {
        if (!personId) return;

        const dataset = source === 'staff' ? staffData : officialData;
        const person  = dataset.find(p => p.id == personId);
        if (!person) return;

        const map = autofillMap[docTypeKey] || {};

        document.querySelectorAll(`#form-${docTypeKey} [data-field-key]`).forEach(function(wrapper) {
            const fieldKey    = wrapper.dataset.fieldKey;
            const fieldConfig = map[fieldKey];
            if (!fieldConfig) return;

            // Only fill fields that match this autofill role
            if (fieldConfig.role !== autofillRole) return;

            const col   = fieldConfig.col;
            const input = wrapper.querySelector('input, select, textarea');
            if (input && person[col] !== undefined && person[col] !== null) {
                input.value = person[col];
            }
        });
    }

    // ----------------------------------------------------------------
    // Show / hide form sections + disable hidden inputs
    // ----------------------------------------------------------------
    function showForm(selectedKey) {
        document.querySelectorAll('[id^="form-"]').forEach(function(el) {
            el.classList.add('hidden');
            el.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.disabled = true;
            });
        });
        const target = document.getElementById('form-' + selectedKey);
        if (target) {
            target.classList.remove('hidden');
            target.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.disabled = false;
            });
        }
    }

    // ----------------------------------------------------------------
    // Consent check before submit
    // ----------------------------------------------------------------
    function submitIfConsented() {
        if (!document.getElementById('consent').checked) {
            alert('Mohon centang pernyataan persetujuan terlebih dahulu.');
            return;
        }
        // Disable hidden sections before submitting
        document.querySelectorAll('[id^="form-"]').forEach(function(section) {
            if (section.classList.contains('hidden')) {
                section.querySelectorAll('input, select, textarea').forEach(function(input) {
                    input.disabled = true;
                });
            }
        });
        document.getElementById('main-form').submit();
    }

    // ----------------------------------------------------------------
    // Repeating group rows
    // ----------------------------------------------------------------
    const rowCounters = {};

    function addRow(docTypeKey, groupKey) {
        const container = document.getElementById(`rows-${docTypeKey}-${groupKey}`);
        const template  = document.getElementById(`row-template-${docTypeKey}-${groupKey}`);
        if (!container || !template) return;

        const key   = `${docTypeKey}-${groupKey}`;
        const index = rowCounters[key] = (rowCounters[key] || 0) + 1;

        const clone = template.content.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(function(el) {
            el.name = el.name.replace('__INDEX__', index);
        });
        container.appendChild(clone);
    }

    function removeRow(btn) {
        btn.closest('.row-item').remove();
    }

    // ----------------------------------------------------------------
    // Init
    // ----------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function() {
        loadAllData();
        const select = document.getElementById('letter-type-select');
        if (select) showForm(select.value);
    });
    </script>

</body>
</html>