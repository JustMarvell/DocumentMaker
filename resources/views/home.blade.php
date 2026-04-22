<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Automatisasi Surat — DINAS PUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
        <span class="font-bold text-gray-800">DINAS PUPRD Kota Tomohon</span>
        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                    <span class="ml-1 px-2 py-0.5 rounded text-xs font-semibold
                        {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ auth()->user()->role === 'staff' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ auth()->user()->role === 'guest' ? 'bg-gray-100 text-gray-600' : '' }}">
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
            <p class="text-sm text-gray-500 mt-1">
                Pilih jenis dokumen dan isi form yang tersedia.
            </p>
        </div>

        {{-- Flash messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Surat / Dokumen
                        </label>
                        <select name="letter-type" id="letter-type-select"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="showForm(this.value)">
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->key }}"
                                    data-autofill-role="{{ $type->staff_autofill_role }}">
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @foreach ($documentTypes as $docType)
                        @php
            $fields = $allFields[$docType->id] ?? collect();
            $topFields = $fields->where('is_group_child', false);
            $autofillRole = $docType->staff_autofill_role;
                        @endphp

                        <div id="form-{{ $docType->key }}"
                             class="{{ !$loop->first ? 'hidden' : '' }}">

                            @if (in_array($autofillRole, ['employee', 'both']))
                                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <label class="block text-sm font-medium text-blue-700 mb-1">
                                        Pilih Pegawai (opsional — mengisi otomatis)
                                    </label>
                                    <select onchange="fillFromStaff('{{ $docType->key }}', 'employee', this.value)"
                                        class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 staff-dropdown">
                                        <option value="">— Pilih staff untuk mengisi otomatis —</option>
                                    </select>
                                </div>
                            @endif

                            @if (in_array($autofillRole, ['appraiser', 'both']))
                                <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                    <label class="block text-sm font-medium text-indigo-700 mb-1">
                                        Pilih Penilai (opsional — mengisi otomatis)
                                    </label>
                                    <select onchange="fillFromStaff('{{ $docType->key }}', 'appraiser', this.value)"
                                        class="w-full border border-indigo-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 staff-dropdown">
                                        <option value="">— Pilih staff untuk mengisi otomatis —</option>
                                    </select>
                                </div>
                            @endif

                            @php $currentSection = null; @endphp

                            @foreach ($topFields as $field)

                                {{-- Section heading --}}
                                @if ($field->section_label && $field->section_label !== $currentSection)
                                    @php $currentSection = $field->section_label; @endphp
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-5 mb-3 border-b pb-2">
                                        {{ $field->section_label }}
                                    </h3>
                                @endif

                                @if ($field->field_type === 'repeating_group')
                                    @php
                    $children = $fields
                        ->where('is_group_child', true)
                        ->where('group_key', $field->field_key);
                                    @endphp

                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-sm font-medium text-gray-700">
                                                {{ $field->label }}
                                                @if ($field->is_required)
                                                    <span class="text-red-500">*</span>
                                                @endif
                                            </label>
                                            <button type="button"
                                                onclick="addRow('{{ $docType->key }}', '{{ $field->field_key }}')"
                                                class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                + Tambah Baris
                                            </button>
                                        </div>

                                        {{-- Column headers --}}
                                        <div class="grid gap-2 mb-1"
                                             style="grid-template-columns: repeat({{ $children->count() }}, 1fr) auto">
                                            @foreach ($children as $child)
                                                <span class="text-xs font-medium text-gray-500">{{ $child->label }}</span>
                                            @endforeach
                                            <span></span>
                                        </div>

                                        {{-- Rows container --}}
                                        <div id="rows-{{ $docType->key }}-{{ $field->field_key }}">
                                            {{-- Rows added by JS --}}
                                        </div>

                                        {{-- Hidden template for JS cloning --}}
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
                                         data-autofill-col="{{ $field->staff_autofill_column ?? '' }}">

                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        @php
                    $inputClass = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
                    $inputName = "field_{$field->field_key}";
                    $oldValue = old($inputName, '');
                    $required = $field->is_required ? 'required' : '';
                                        @endphp

                                        @if ($field->field_type === 'textarea')
                                            <textarea name="{{ $inputName }}" rows="3"
                                                class="{{ $inputClass }}" {{ $required }}>{{ $oldValue }}</textarea>

                                        @elseif ($field->field_type === 'date')
                                            <input type="date" name="{{ $inputName }}"
                                                value="{{ $oldValue }}"
                                                class="{{ $inputClass }}" {{ $required }} />

                                        @elseif ($field->field_type === 'number')
                                            <input type="number" name="{{ $inputName }}"
                                                value="{{ $oldValue }}"
                                                class="{{ $inputClass }}" {{ $required }} />

                                        @elseif ($field->field_type === 'select')
                                            <select name="{{ $inputName }}"
                                                class="{{ $inputClass }}" {{ $required }}>
                                                <option value="">— Pilih —</option>
                                                @foreach ($field->field_options ?? [] as $option)
                                                    <option value="{{ $option }}"
                                                        {{ $oldValue === $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        @elseif ($field->field_type === 'checkbox')
                                            <div class="flex items-center gap-2 mt-1">
                                                <input type="checkbox" name="{{ $inputName }}"
                                                    value="1" id="field-{{ $field->field_key }}-{{ $docType->key }}"
                                                    {{ $oldValue ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600" />
                                                <label for="field-{{ $field->field_key }}-{{ $docType->key }}"
                                                    class="text-sm text-gray-600">
                                                    {{ $field->label }}
                                                </label>
                                            </div>

                                        @else
                                            {{-- Default: text --}}
                                            <input type="text" name="{{ $inputName }}"
                                                value="{{ $oldValue }}"
                                                class="{{ $inputClass }}" {{ $required }}
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

    <script>
    // Field autofill map: { docTypeKey: { fieldKey: staffColumn } }
    const autofillMap = {
        @foreach ($documentTypes as $docType)
        "{{ $docType->key }}": {
            @foreach (($allFields[$docType->id] ?? collect())->whereNotNull('staff_autofill_column') as $f)
            "{{ $f->field_key }}": "{{ $f->staff_autofill_column }}",
            @endforeach
        },
        @endforeach
    };

    let staffData = [];

    async function loadStaffData() {
        try {
            const res = await fetch('{{ route('api.staff') }}');
            staffData = await res.json();
            populateStaffDropdowns();
        } catch(e) {
            console.warn('Could not load staff data:', e);
        }
    }

    function populateStaffDropdowns() {
        document.querySelectorAll('.staff-dropdown').forEach(function(select) {
            const placeholder = select.options[0];
            select.innerHTML = '';
            select.appendChild(placeholder);
            staffData.forEach(function(staff) {
                const opt = document.createElement('option');
                opt.value = staff.id;
                opt.textContent = staff.staff_name + (staff.nip ? ' — ' + staff.nip : '');
                select.appendChild(opt);
            });
        });
    }

    function fillFromStaff(docTypeKey, role, staffId) {
        if (!staffId) return;
        const staff = staffData.find(s => s.id == staffId);
        if (!staff) return;

        const map = autofillMap[docTypeKey] || {};

        document.querySelectorAll(`#form-${docTypeKey} [data-field-key]`).forEach(function(wrapper) {
            const fieldKey    = wrapper.dataset.fieldKey;
            const autofillCol = wrapper.dataset.autofillCol;
            if (!autofillCol) return;

            // For 'both' templates, determine which fields belong to which role
            // by checking if the field_key contains 'appraisal' — appraisal fields
            // are filled by the appraiser selector, all others by the employee selector
            const isAppraisalField = fieldKey.startsWith('appraisal_');
            if (role === 'employee'  &&  isAppraisalField) return;
            if (role === 'appraiser' && !isAppraisalField) return;

            const input = wrapper.querySelector('input, select, textarea');
            if (input && staff[autofillCol] !== undefined && staff[autofillCol] !== null) {
                input.value = staff[autofillCol];
            }
        });
    }

    function showForm(selectedKey) {
        document.querySelectorAll('[id^="form-"]').forEach(function(el) {
            el.classList.add('hidden');
        });
        const target = document.getElementById('form-' + selectedKey);
        if (target) {
            target.classList.remove('hidden');
            // Re-enable inputs in the visible section
            target.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.disabled = false;
            });
        }
    }

    const rowCounters = {};

    function addRow(docTypeKey, groupKey) {
        const containerId = `rows-${docTypeKey}-${groupKey}`;
        const templateId  = `row-template-${docTypeKey}-${groupKey}`;
        const container   = document.getElementById(containerId);
        const template    = document.getElementById(templateId);
        if (!container || !template) return;

        const key   = `${docTypeKey}-${groupKey}`;
        const index = rowCounters[key] = (rowCounters[key] || 0) + 1;

        const clone = template.content.cloneNode(true);
        // Replace __INDEX__ placeholder with actual index
        clone.querySelectorAll('[name]').forEach(function(el) {
            el.name = el.name.replace('__INDEX__', index);
        });
        container.appendChild(clone);
    }

    function removeRow(btn) {
        btn.closest('.row-item').remove();
    }

    function submitIfConsented() {
        if (!document.getElementById('consent').checked) {
            alert('Mohon centang pernyataan persetujuan terlebih dahulu.');
            return;
        }

        // Disable all inputs in hidden form sections so they don't submit
        document.querySelectorAll('[id^="form-"]').forEach(function(section) {
            const isHidden = section.classList.contains('hidden');
            section.querySelectorAll('input, select, textarea').forEach(function(input) {
                input.disabled = isHidden;
            });
        });

        document.getElementById('main-form').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadStaffData();
        const select = document.getElementById('letter-type-select');
        if (select) showForm(select.value);
    });
    </script>

</body>
</html>