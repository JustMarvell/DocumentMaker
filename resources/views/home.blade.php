<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Automatisasi Surat — DINAS PUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .loop-item {
            cursor: grab;
        }

        .loop-item.dragging {
            opacity: 0.5;
        }

        .loop-item:active {
            cursor: grabbing;
        }
    </style>
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
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Login</a>
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
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
                <p class="text-sm mb-2">{{ session('success') }}</p>
                <div class="flex gap-3 flex-wrap">
                    @if (session('download_url'))
                        <a href="{{ session('download_url') }}"
                            class="bg-green-600 text-white text-sm px-4 py-1.5 rounded hover:bg-green-700 font-medium">
                            ⬇ Unduh Dokumen
                        </a>
                    @endif
                    @if (session('preview_url'))
                        <button type="button" onclick="openPreview('{{ session('preview_url') }}')"
                            class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded hover:bg-blue-700 font-medium">
                            👁 Preview Dokumen
                        </button>
                    @endif
                </div>
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
                    
                    @foreach ($documentTypes as $docType)
                        @php
        $fields = $allFields[$docType->id] ?? collect();
        $topFields = $fields->where('is_group_child', false);
        $slots = $docType->slots;
                        @endphp

                        <div id="form-{{ $docType->key }}" class="{{ !$loop->first ? 'hidden' : '' }}">

                            @foreach ($slots as $slot)
                                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm font-medium text-blue-700 mb-2">
                                        Pilih {{ $slot->slot_label }} (opsional — mengisi otomatis)
                                    </p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs text-blue-600 mb-1">Dari Data Staff</label>
                                            <select
                                                onchange="fillFromSource('{{ $docType->key }}', '{{ $slot->slot_key }}', 'staff', this.value)"
                                                class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 staff-dropdown">
                                                <option value="">— Pilih Staff —</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-blue-600 mb-1">Dari Data Pejabat</label>
                                            <select
                                                onchange="fillFromSource('{{ $docType->key }}', '{{ $slot->slot_key }}', 'official', this.value)"
                                                class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 official-dropdown">
                                                <option value="">— Pilih Pejabat —</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @php
        $currentSection = null;
        $currentRowGroup = null;
        $rowGroupBuffer = [];

        // Group top-level fields into renderable chunks:
        // Each chunk is either a single field (row_group=null)
        // or a collection of fields sharing the same row_group
        $chunks = [];
        foreach ($topFields as $field) {
            if (is_null($field->row_group)) {
                $chunks[] = ['type' => 'single', 'field' => $field];
            } else {
                // Find or create a row group chunk
                $found = false;
                foreach ($chunks as &$chunk) {
                    if ($chunk['type'] === 'row' && $chunk['row_group'] === $field->row_group) {
                        $chunk['fields'][] = $field;
                        $found = true;
                        break;
                    }
                }
                unset($chunk);
                if (!$found) {
                    $chunks[] = ['type' => 'row', 'row_group' => $field->row_group, 'fields' => [$field]];
                }
            }
        }
                            @endphp

                            @foreach ($chunks as $chunk)
                                @if ($chunk['type'] === 'single')
                                    @php $field = $chunk['field']; @endphp

                                    {{-- Section heading --}}
                                    @if ($field->section_label && $field->section_label !== $currentSection)
                                        @php $currentSection = $field->section_label; @endphp
                                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-5 mb-3 border-b pb-2">
                                            {{ $field->section_label }}
                                        </h3>
                                    @endif

                                    <div class="mb-4">
                                        @include('partials.form-field', ['field' => $field, 'docType' => $docType, 'fields' => $fields])
                                    </div>

                                @else
                                    {{-- Row group: render fields side by side --}}
                                    @php $firstField = $chunk['fields'][0]; @endphp

                                    {{-- Section heading from first field in group --}}
                                    @if ($firstField->section_label && $firstField->section_label !== $currentSection)
                                        @php $currentSection = $firstField->section_label; @endphp
                                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-5 mb-3 border-b pb-2">
                                            {{ $firstField->section_label }}
                                        </h3>
                                    @endif

                                    <div class="grid gap-4 mb-4"
                                        style="grid-template-columns: repeat({{ count($chunk['fields']) }}, 1fr)">
                                        @foreach ($chunk['fields'] as $field)
                                            <div>
                                                @include('partials.form-field', ['field' => $field, 'docType' => $docType, 'fields' => $fields])
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    @endforeach

                    {{-- Consent + Submit --}}
                    <div class="mt-6 pt-4 border-t flex items-center gap-3">
                        <input type="checkbox" name="consent" id="consent" class="rounded border-gray-300 text-blue-600" />
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
    {{-- Autofill map: { docKey: { fieldKey: { col, role } } } --}}
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

        let staffData = [];
        let officialData = [];

        async function loadAllData() {
            try {
                const [sRes, oRes] = await Promise.all([
                    fetch('{{ route('api.staff') }}'),
                    fetch('{{ route('api.officials') }}'),
                ]);
                staffData = await sRes.json();
                officialData = await oRes.json();
                populateDropdowns();
                populateLoopLists();
            } catch (e) {
                console.warn('Could not load autofill data:', e);
            }
        }

        function populateDropdowns() {
            document.querySelectorAll('.staff-dropdown').forEach(function (select) {
                const ph = select.options[0];
                select.innerHTML = '';
                select.appendChild(ph);
                staffData.forEach(function (p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.staff_name + (p.nip ? ' — ' + p.nip : '');
                    select.appendChild(opt);
                });
            });
            document.querySelectorAll('.official-dropdown').forEach(function (select) {
                const ph = select.options[0];
                select.innerHTML = '';
                select.appendChild(ph);
                officialData.forEach(function (p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.staff_name + (p.nip ? ' — ' + p.nip : '');
                    select.appendChild(opt);
                });
            });
        }
        
        function populateLoopLists() {
            document.querySelectorAll('[data-loop-type]').forEach(function (container) {
                const loopType = container.dataset.loopType;   // 'staff' or 'official'
                const fieldKey = container.dataset.fieldKey;
                const docKey = container.dataset.docKey;
                const dataset = loopType === 'staff' ? staffData : officialData;
                const listEl = container.querySelector('.loop-checklist');
                const searchEl = container.querySelector('.loop-search');
                if (!listEl) return;

                listEl.innerHTML = '';
                dataset.forEach(function (person) {
                    listEl.appendChild(makeLoopItem(person, fieldKey));
                });

                if (searchEl) {
                    searchEl.addEventListener('input', function () {
                        const q = this.value.toLowerCase();
                        listEl.querySelectorAll('.loop-item').forEach(function (item) {
                            item.style.display = item.dataset.name.toLowerCase().includes(q) ? '' : 'none';
                        });
                    });
                }

                initLoopDrag(listEl, fieldKey);
            });
        }

        function makeLoopItem(person, fieldKey) {
            const div = document.createElement('div');
            div.className = 'loop-item flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-50 border border-transparent hover:border-gray-200';
            div.dataset.id = person.id;
            div.dataset.name = person.staff_name;
            div.setAttribute('draggable', true);

            const cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.name = `field_${fieldKey}[]`;
            cb.value = person.id;
            cb.className = 'rounded border-gray-300 text-blue-600 flex-shrink-0';

            const label = document.createElement('span');
            label.className = 'text-sm text-gray-700 flex-1';
            label.textContent = person.staff_name + (person.nip ? ' — ' + person.nip : '') + (person.position ? ' (' + person.position + ')' : '');

            const handle = document.createElement('span');
            handle.className = 'text-gray-300 text-base select-none cursor-grab';
            handle.textContent = '⠿';

            div.appendChild(cb);
            div.appendChild(label);
            div.appendChild(handle);
            return div;
        }

        function initLoopDrag(listEl) {
            let dragging = null;

            listEl.addEventListener('dragstart', function (e) {
                dragging = e.target.closest('.loop-item');
                if (dragging) dragging.classList.add('dragging');
            });
            listEl.addEventListener('dragend', function () {
                if (dragging) dragging.classList.remove('dragging');
                dragging = null;
            });
            listEl.addEventListener('dragover', function (e) {
                e.preventDefault();
                const target = e.target.closest('.loop-item');
                if (target && target !== dragging) {
                    const rect = target.getBoundingClientRect();
                    const after = (e.clientY - rect.top) > (rect.height / 2);
                    listEl.insertBefore(dragging, after ? target.nextSibling : target);
                }
            });
        }
        
        function fillFromSource(docTypeKey, slotKey, source, personId) {
            if (!personId) return;
            const dataset = source === 'staff' ? staffData : officialData;
            const person = dataset.find(p => p.id == personId);
            if (!person) return;

            const map = autofillMap[docTypeKey] || {};

            document.querySelectorAll(`#form-${docTypeKey} [data-field-key]`).forEach(function (wrapper) {
                const fieldKey = wrapper.dataset.fieldKey;
                const fieldConfig = map[fieldKey];
                if (!fieldConfig) return;
                if (fieldConfig.role !== slotKey) return;

                const col = fieldConfig.col;
                const input = wrapper.querySelector('input, select, textarea');
                if (input && person[col] !== undefined && person[col] !== null) {
                    input.value = person[col];
                }
            });
        }
        
        function showForm(selectedKey) {
            document.querySelectorAll('[id^="form-"]').forEach(function (el) {
                el.classList.add('hidden');
                el.querySelectorAll('input, select, textarea').forEach(function (i) { i.disabled = true; });
            });
            const target = document.getElementById('form-' + selectedKey);
            if (target) {
                target.classList.remove('hidden');
                target.querySelectorAll('input, select, textarea').forEach(function (i) { i.disabled = false; });
            }
        }
        
        function submitIfConsented() {
            if (!document.getElementById('consent').checked) {
                alert('Mohon centang pernyataan persetujuan terlebih dahulu.');
                return;
            }
            document.querySelectorAll('[id^="form-"]').forEach(function (section) {
                if (section.classList.contains('hidden')) {
                    section.querySelectorAll('input, select, textarea').forEach(function (i) { i.disabled = true; });
                }
            });
            document.getElementById('main-form').submit();
        }

        const rowCounters = {};

        function addRow(docTypeKey, groupKey) {
            const container = document.getElementById(`rows-${docTypeKey}-${groupKey}`);
            const template = document.getElementById(`row-template-${docTypeKey}-${groupKey}`);
            if (!container || !template) return;

            const key = `${docTypeKey}-${groupKey}`;
            const index = rowCounters[key] = (rowCounters[key] || 0) + 1;

            const clone = template.content.cloneNode(true);
            clone.querySelectorAll('[name]').forEach(function (el) {
                el.name = el.name.replace('__INDEX__', index);
            });
            container.appendChild(clone);
        }

        function removeRow(btn) {
            btn.closest('.row-item').remove();
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadAllData();
            const select = document.getElementById('letter-type-select');
            if (select) showForm(select.value);
        });
    </script>

    <div id="preview-modal"
        class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden"
        onclick="if(event.target===this) closePreview()">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl mx-4 flex flex-col"
            style="height: 90vh;">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-5 py-3 border-b flex-shrink-0">
                <h2 class="text-base font-semibold text-gray-800">Preview Dokumen</h2>
                <div class="flex items-center gap-3">
                    <a id="preview-download-btn" href="#"
                    class="text-sm bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700 font-medium hidden">
                        ⬇ Unduh
                    </a>
                    <button onclick="closePreview()"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">
                        ✕
                    </button>
                </div>
            </div>

            {{-- Loading state --}}
            <div id="preview-loading"
                class="flex-1 flex items-center justify-center text-gray-400 text-sm">
                <div class="text-center">
                    <div class="text-3xl mb-2">⏳</div>
                    <p>Memuat preview...</p>
                    <p class="text-xs mt-1 text-gray-300">Proses konversi mungkin memerlukan beberapa detik</p>
                </div>
            </div>

            {{-- Error state --}}
            <div id="preview-error"
                class="flex-1 flex items-center justify-center text-red-400 text-sm hidden">
                <div class="text-center">
                    <div class="text-3xl mb-2">⚠️</div>
                    <p class="font-medium">Gagal memuat preview</p>
                    <p class="text-xs mt-1 text-gray-400" id="preview-error-msg"></p>
                </div>
            </div>

            {{-- iframe --}}
            <iframe id="preview-iframe"
                    class="flex-1 w-full hidden rounded-b-lg"
                    src=""
                    title="Document Preview">
            </iframe>

        </div>
    </div>

    <script>
        function openPreview(previewUrl) {
            const modal   = document.getElementById('preview-modal');
            const iframe  = document.getElementById('preview-iframe');
            const loading = document.getElementById('preview-loading');
            const error   = document.getElementById('preview-error');

            // Reset state
            iframe.classList.add('hidden');
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            iframe.src = '';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Load the PDF
            iframe.onload = function() {
                loading.classList.add('hidden');
                iframe.classList.remove('hidden');
            };

            iframe.onerror = function() {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('preview-error-msg').textContent =
                    'Pastikan LibreOffice terinstall di server.';
            };

            iframe.src = previewUrl;
        }

        function closePreview() {
            const modal  = document.getElementById('preview-modal');
            const iframe = document.getElementById('preview-iframe');
            modal.classList.add('hidden');
            iframe.src = '';
            document.body.style.overflow = '';
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closePreview();
        });
    </script>

</body>

</html>