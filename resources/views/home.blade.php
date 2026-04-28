<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Automatisasi Surat — DINAS PUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- FontAwesome 6 Free (CDN) — covers fa-solid, fa-regular, fa-brands --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* ── Loop items ────────────────────────────────── */
        .loop-item { cursor: grab; transition: background 0.15s, box-shadow 0.15s; }
        .loop-item.dragging { opacity: 0.4; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .loop-item:active { cursor: grabbing; }
        .loop-item.checked-item { background: #EFF6FF; border-color: #BFDBFE !important; }

        /* ── Form section transitions ───────────────────── */
        .form-section {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .form-section.entering {
            opacity: 0;
            transform: translateY(8px);
        }
        .form-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Autofill highlight flash ───────────────────── */
        @keyframes autofillFlash {
            0%   { background-color: #FEF9C3; }
            100% { background-color: transparent; }
        }
        .autofill-highlight {
            animation: autofillFlash 1.2s ease-out forwards;
            border-color: #FCD34D !important;
        }

        /* ── Loading overlay ────────────────────────────── */
        #submit-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(2px);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }
        #submit-overlay.active { display: flex; }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 44px; height: 44px;
            border: 4px solid #E5E7EB;
            border-top-color: #2563EB;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* ── Page entrance ──────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.4s ease-out both; }
        .fade-up-delay-1 { animation-delay: 0.05s; }
        .fade-up-delay-2 { animation-delay: 0.10s; }
        .fade-up-delay-3 { animation-delay: 0.15s; }

        /* ── Smooth scroll ──────────────────────────────── */
        html { scroll-behavior: smooth; }

        /* ── Submit button states ───────────────────────── */
        #submit-btn { transition: background 0.2s, transform 0.1s; }
        #submit-btn:active { transform: scale(0.98); }
        #submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }

        /* ── Input focus ring enhancement ───────────────── */
        input:focus, select:focus, textarea:focus {
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        /* ── Guide panel cards ──────────────────────────── */
        .guide-card { transition: box-shadow 0.2s; }
        .guide-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

        /* ── Mobile: row group grid collapses to single column ── */
        @media (max-width: 640px) {
            .row-group-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* ── Mobile: page header smaller ───────────────────── */
        @media (max-width: 640px) {
            .page-title { font-size: 1.25rem; }
        }

        /* ── Mobile: loop checklist taller for touch ────────── */
        @media (max-width: 640px) {
            .loop-checklist { max-height: 200px; }
            .loop-item { padding: 10px 12px; }
        }

        /* ── Mobile: submit button larger touch target ──────── */
        @media (max-width: 640px) {
            #submit-btn { padding-top: 0.875rem; padding-bottom: 0.875rem; font-size: 1rem; }
        }

        /* ── Mobile: consent area wraps nicely ──────────────── */
        @media (max-width: 400px) {
            .consent-wrap { align-items: flex-start; }
        }

        /* ── Prevent horizontal scroll ──────────────────────── */
        body { overflow-x: hidden; }
    </style>
</head>

<body class="bg-gray-100 font-sans">

    {{-- Navbar --}}
    <nav class="bg-white shadow px-4 sm:px-6 py-3 sm:py-4" x-data="{ menuOpen: false }">
        <div class="flex items-center justify-between">
            <span class="font-bold text-gray-800 text-sm sm:text-base leading-tight">
                DINAS PUPRD<br class="sm:hidden"> <span class="hidden sm:inline">Kota Tomohon</span>
                <span class="sm:hidden text-xs font-normal text-gray-500">Kota Tomohon</span>
            </span>

            {{-- Desktop nav --}}
            <div class="hidden sm:flex items-center gap-4">
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

            {{-- Mobile hamburger --}}
            <button class="sm:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100"
                    @click="menuOpen = !menuOpen" aria-label="Menu">
                <svg x-show="!menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile dropdown menu --}}
        <div x-show="menuOpen" x-transition class="sm:hidden mt-3 pt-3 border-t border-gray-100 space-y-2">
            @auth
                <div class="flex items-center gap-2 px-1 pb-2 border-b border-gray-100">
                    <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold
                        {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                        {{ auth()->user()->role === 'staff' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ auth()->user()->role === 'guest' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-1 py-1.5 text-sm text-purple-600 font-medium">
                        Admin Panel
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-1 py-1.5 text-sm text-red-500">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"    class="block px-1 py-1.5 text-sm text-blue-600">Login</a>
                <a href="{{ route('register') }}" class="block px-1 py-1.5 text-sm text-blue-600">Daftar</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-4 sm:py-8">

        {{--Loading overlay --}}
        <div id="submit-overlay">
            <div class="spinner"></div>
            <p class="text-sm font-medium text-gray-600">Sedang membuat dokumen...</p>
            <p class="text-xs text-gray-400">Mohon tunggu, jangan tutup halaman ini.</p>
        </div>

        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 page-title">Sistem Automatisasi Surat</h1>
            <p class="text-sm text-gray-500 mt-1">Pilih jenis dokumen dan isi form yang tersedia.</p>
        </div>

        {{-- Flash messages --}}
        <div class="fade-up fade-up-delay-1">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
                <p class="text-sm mb-3">{{ session('success') }}</p>
                <div class="flex flex-wrap gap-3">
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
                        <!-- Left Column -->
                        <div class="flex flex-col lg:flex-row gap-6 items-start fade-up fade-up-delay-2">

                            {{-- Left column: form --}}
                            <div class="flex-1 min-w-0 w-full">
                            <div class="bg-white rounded-lg shadow p-4 sm:p-6">
                                <form action="{{ route('document.generate') }}" method="POST" id="main-form">
                                    @csrf

                                    {{-- Document type selector --}}
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Surat / Dokumen</label>
                                        <select name="letter-type" id="letter-type-select"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors cursor-pointer"
                                            onchange="showForm(this.value)">
                                            @foreach ($documentTypes as $type)
                                                <option value="{{ $type->key }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- ============================================================ --}}
                                    {{-- Dynamic form sections — one per document type                --}}
                                    {{-- ============================================================ --}}
                                    @foreach ($documentTypes as $docType)
                                        @php
        $fields = $allFields[$docType->id] ?? collect();
        $topFields = $fields->where('is_group_child', false);
        $slots = $docType->slots;
                                        @endphp

                                        <div id="form-{{ $docType->key }}" class="{{ !$loop->first ? 'hidden' : '' }}">

                                            {{-- -------------------------------------------------- --}}
                                            {{-- Autofill selectors — one pair per slot              --}}
                                            {{-- -------------------------------------------------- --}}
                                            @foreach ($slots as $slot)
                                                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                    <p class="text-sm font-medium text-blue-700 mb-2">
                                                        Pilih {{ $slot->slot_label }} (opsional — mengisi otomatis)
                                                    </p>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

                                            {{-- -------------------------------------------------- --}}
                                            {{-- Render fields — grouped by row_group                --}}
                                            {{-- -------------------------------------------------- --}}
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

                                                    <div class="grid gap-4 mb-4 row-group-grid" style="grid-template-columns: repeat({{ count($chunk['fields']) }}, 1fr)">
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
                                    <div class="mt-6 pt-4 border-t flex items-center gap-3 consent-wrap">
                                        <input type="checkbox" name="consent" id="consent"
                                            class="rounded border-gray-300 text-blue-600" />
                                        <label for="consent" class="text-sm text-gray-600">
                                            Saya menyatakan bahwa informasi yang saya berikan adalah benar adanya.
                                        </label>
                                    </div>
                                    <button type="button" id="submit-btn" onclick="submitIfConsented()"
                                        class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                                        Buat Dokumen
                                    </button>

                                </form>
                            </div>
                            </div>
                            {{-- End left column --}}

                            {{-- ======================================================== --}}
                            {{-- Right column — guide panel                               --}}
                            {{-- ======================================================== --}}
                            <div class="w-full lg:w-72 lg:flex-shrink-0 lg:sticky lg:top-6 space-y-3">

                                {{-- Quick start card --}}
                                <div class="bg-white rounded-lg shadow p-4 guide-card fade-up fade-up-delay-2">
                                    <div class="flex items-center gap-2 mb-3">
                                        <h2 class="text-sm font-bold text-gray-800">Cara Membuat Dokumen</h2>
                                    </div>
                                    <ol class="space-y-2">
                                        @foreach ([
        ['1', 'bg-blue-600', 'Pilih Jenis Dokumen', 'Gunakan dropdown "Jenis Surat / Dokumen" untuk memilih template yang diinginkan.'],
        ['2', 'bg-blue-600', 'Gunakan Autofill (Opsional)', 'Jika tersedia, pilih nama pegawai dari dropdown autofill berwarna biru/ungu untuk mengisi otomatis field seperti nama, NIP, dan jabatan.'],
        ['3', 'bg-blue-600', 'Isi Form', 'Lengkapi semua field yang tersedia. Field bertanda * wajib diisi. Field tanggal otomatis diformat ke format Indonesia.'],
        ['4', 'bg-blue-600', 'Centang Persetujuan', 'Centang pernyataan persetujuan di bagian bawah form sebelum melanjutkan.'],
        ['5', 'bg-blue-600', 'Klik Buat Dokumen', 'Tekan tombol "Buat Dokumen". Sistem akan memproses dan menghasilkan file dokumen.'],
        ['6', 'bg-green-600', 'Unduh / Preview', 'Setelah berhasil, tombol Unduh akan muncul. Tombol Preview muncul jika diaktifkan admin.'],
    ] as [$num, $color, $title, $desc])
                                        <li class="flex gap-3">
                                            <span class="flex-shrink-0 w-5 h-5 {{ $color }} text-white rounded-full flex items-center justify-center text-xs font-bold mt-0.5">{{ $num }}</span>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-700">{{ $title }}</p>
                                                <p class="text-xs text-gray-500 leading-relaxed mt-0.5">{{ $desc }}</p>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ol>
                                </div>

                                {{-- Field types card --}}
                                <div class="bg-white rounded-lg shadow p-4 guide-card fade-up fade-up-delay-3">
                                    <div class="flex items-center gap-2 mb-3">
                                        <h2 class="text-sm font-bold text-gray-800">Jenis Field di Form</h2>
                                    </div>
                                    <div class="space-y-2 text-xs text-gray-600">
                                        @foreach ([
        ['•', 'Text / Textarea', 'Ketik teks bebas. Textarea untuk keterangan panjang.'],
        ['•', 'Date', 'Pilih tanggal dari kalender. Otomatis diformat ke "01 Januari 2025".'],
        ['•', 'Number', 'Ketik angka (jumlah hari, nomor urut, dsb.)'],
        ['•', 'Select (Dropdown)', 'Pilih satu opsi dari daftar yang tersedia.'],
        ['•', 'Checkbox', 'Centang untuk nilai Ya/Benar.'],
        ['•', 'Repeating Group', 'Klik "+ Tambah Baris" untuk menambah baris data. Klik × untuk menghapus.'],
        ['•', 'Staff / Pejabat Loop', 'Centang nama yang ingin dimasukkan. Drag ⠿ untuk mengubah urutan dalam dokumen.'],
    ] as [$icon, $type, $desc])
                                                    <div class="flex gap-2">
                                                        <span class="flex-shrink-0 w-5 text-center">{{ $icon }}</span>
                                                        <div>
                                                            <span class="font-medium text-gray-700">{{ $type }}</span>
                                                            <span class="text-gray-400"> — </span>
                                                            <span>{{ $desc }}</span>
                                                        </div>
                                                    </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Autofill tip card --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h2 class="text-sm font-bold text-blue-800">Tips Autofill</h2>
                                    </div>
                                    <ul class="space-y-1.5 text-xs text-blue-700">
                                        <li>• Setiap slot autofill (biru/ungu) memiliki <strong>dua dropdown</strong>: satu dari Data Staff dan satu dari Data Pejabat.</li>
                                        <li>• Memilih dari salah satu dropdown akan otomatis mengisi nama, NIP, jabatan, dan unit kerja.</li>
                                        <li>• Field yang terisi otomatis masih <strong>bisa diedit manual</strong> jika perlu.</li>
                                        <li>• Untuk daftar peserta (Staff/Pejabat Loop), centang beberapa nama lalu <strong>drag untuk mengubah urutan</strong> dalam dokumen.</li>
                                    </ul>
                                </div>

                                {{-- Warning card --}}
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h2 class="text-sm font-bold text-yellow-800">Perhatian</h2>
                                    </div>
                                    <ul class="space-y-1.5 text-xs text-yellow-700">
                                        <li>• File dokumen akan <strong>otomatis terhapus</strong> dari server setelah beberapa menit. Segera unduh setelah dibuat.</li>
                                        <li>• Pastikan semua field wajib (*) terisi sebelum menekan "Buat Dokumen".</li>
                                        <li>• Jika ada field yang kurang atau tidak sesuai, hubungi administrator.</li>
                                    </ul>
                                </div>

                            </div>
                            {{-- End right column --}}

                            </div>
                            {{-- End two-column grid --}}

        @endif
    </main>
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

        function makeLoopItem(person, fieldKey, countEl) {
            const div = document.createElement('div');
            div.className = 'loop-item flex items-center gap-2 px-3 py-2 rounded border border-transparent';
            div.dataset.id   = person.id;
            div.dataset.name = person.staff_name;
            div.setAttribute('draggable', true);

            const cb = document.createElement('input');
            cb.type      = 'checkbox';
            cb.name      = `field_${fieldKey}[]`;
            cb.value     = person.id;
            cb.className = 'rounded border-gray-300 text-blue-600 flex-shrink-0 cursor-pointer';

            // Toggle checked-item highlight and update counter
            cb.addEventListener('change', function() {
                if (this.checked) {
                    div.classList.add('checked-item');
                } else {
                    div.classList.remove('checked-item');
                }
                updateLoopCount(div.closest('[data-loop-type]'));
            });

            const label = document.createElement('span');
            label.className = 'text-sm text-gray-700 flex-1 cursor-pointer select-none';
            label.textContent = person.staff_name
                + (person.nip      ? ' — ' + person.nip      : '')
                + (person.position ? ' (' + person.position + ')' : '');
            label.addEventListener('click', function() { cb.click(); });

            const handle = document.createElement('span');
            handle.className = 'text-gray-300 text-base select-none cursor-grab flex-shrink-0';
            handle.textContent = '⠿';

            div.appendChild(cb);
            div.appendChild(label);
            div.appendChild(handle);
            return div;
        }

        function updateLoopCount(container) {
            if (!container) return;
            const countEl  = container.querySelector('.loop-count');
            const checked  = container.querySelectorAll('.loop-item input[type="checkbox"]:checked').length;
            if (!countEl) return;
            if (checked === 0) {
                countEl.textContent = '';
                countEl.classList.add('hidden');
            } else {
                countEl.textContent = checked + ' dipilih';
                countEl.classList.remove('hidden');
            }
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
            const person  = dataset.find(p => p.id == personId);
            if (!person) return;

            const map = autofillMap[docTypeKey] || {};
            let filledCount = 0;

            document.querySelectorAll(`#form-${docTypeKey} [data-field-key]`).forEach(function(wrapper) {
                const fieldKey    = wrapper.dataset.fieldKey;
                const fieldConfig = map[fieldKey];
                if (!fieldConfig) return;
                if (fieldConfig.role !== slotKey) return;

                const col   = fieldConfig.col;
                const input = wrapper.querySelector('input, select, textarea');
                if (input && person[col] !== undefined && person[col] !== null) {
                    input.value = person[col];
                    // Flash highlight
                    input.classList.remove('autofill-highlight');
                    void input.offsetWidth; // force reflow to restart animation
                    input.classList.add('autofill-highlight');
                    setTimeout(() => input.classList.remove('autofill-highlight'), 1300);
                    filledCount++;
                }
            });

            // Brief toast feedback
            if (filledCount > 0) {
                showToast(`${filledCount} field terisi otomatis dari data ${source === 'staff' ? 'staff' : 'pejabat'}.`);
            }
        }

        let toastTimer = null;
        function showToast(message, type = 'success') {
            let toast = document.getElementById('toast-notification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast-notification';
                toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-2.5 rounded-lg shadow-lg text-sm font-medium text-white z-50 transition-all duration-300 opacity-0 translate-y-2';
                document.body.appendChild(toast);
            }
            toast.textContent = message;
            toast.style.background = type === 'success' ? '#2563EB' : '#DC2626';
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-y-2');
                toast.classList.add('opacity-100', 'translate-y-0');
            });
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                toast.classList.remove('opacity-100', 'translate-y-0');
            }, 2500);
        }
        
        function showForm(selectedKey) {
            document.querySelectorAll('[id^="form-"]').forEach(function(el) {
                if (!el.classList.contains('hidden')) {
                    // Fade out current
                    el.style.opacity    = '0';
                    el.style.transform  = 'translateY(4px)';
                    el.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
                    setTimeout(() => {
                        el.classList.add('hidden');
                        el.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                        el.style.opacity   = '';
                        el.style.transform = '';
                    }, 150);
                } else {
                    el.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                }
            });

            setTimeout(() => {
                const target = document.getElementById('form-' + selectedKey);
                if (target) {
                    target.classList.remove('hidden');
                    target.querySelectorAll('input, select, textarea').forEach(i => i.disabled = false);
                    // Fade in
                    target.style.opacity   = '0';
                    target.style.transform = 'translateY(8px)';
                    target.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                    requestAnimationFrame(() => {
                        target.style.opacity   = '1';
                        target.style.transform = 'translateY(0)';
                    });
                    setTimeout(() => {
                        target.style.transition = '';
                        target.style.opacity    = '';
                        target.style.transform  = '';
                    }, 220);
                }
            }, 160);
        }
        
        function submitIfConsented() {
            if (!document.getElementById('consent').checked) {
                showToast('Mohon centang pernyataan persetujuan terlebih dahulu.', 'error');
                // Shake the consent area
                const consentArea = document.getElementById('consent').closest('div');
                consentArea.style.animation = 'none';
                consentArea.style.transition = 'transform 0.1s';
                const shakes = [4, -4, 3, -3, 2, -2, 0];
                shakes.forEach((x, i) => {
                    setTimeout(() => consentArea.style.transform = `translateX(${x}px)`, i * 50);
                });
                setTimeout(() => consentArea.style.transform = '', shakes.length * 50);
                return;
            }

            // Disable hidden sections
            document.querySelectorAll('[id^="form-"]').forEach(function(section) {
                if (section.classList.contains('hidden')) {
                    section.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                }
            });

            // Show loading overlay and disable button
            const btn = document.getElementById('submit-btn');
            if (btn) {
                btn.disabled    = true;
                btn.textContent = 'Membuat dokumen...';
            }
            document.getElementById('submit-overlay').classList.add('active');

            document.getElementById('main-form').submit();
        }

        const rowCounters = {};

        function addRow(docTypeKey, groupKey) {
            const container = document.getElementById(`rows-${docTypeKey}-${groupKey}`);
            const template  = document.getElementById(`row-template-${docTypeKey}-${groupKey}`);
            if (!container || !template) return;

            const key   = `${docTypeKey}-${groupKey}`;
            const index = rowCounters[key] = (rowCounters[key] || 0) + 1;

            const clone = template.content.cloneNode(true);
            clone.querySelectorAll('[name]').forEach(el => el.name = el.name.replace('__INDEX__', index));

            // Animate new row in
            const rowDiv = clone.querySelector('.row-item');
            if (rowDiv) {
                rowDiv.style.opacity   = '0';
                rowDiv.style.transform = 'translateY(-6px)';
                rowDiv.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            }
            container.appendChild(clone);
            // Trigger animation after append
            const newRow = container.lastElementChild;
            if (newRow) {
                requestAnimationFrame(() => {
                    newRow.style.opacity   = '1';
                    newRow.style.transform = 'translateY(0)';
                    setTimeout(() => {
                        newRow.style.transition = '';
                        newRow.style.opacity    = '';
                        newRow.style.transform  = '';
                    }, 220);
                });
            }
        }

        function removeRow(btn) {
            const row = btn.closest('.row-item');
            if (!row) return;
            row.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
            row.style.opacity    = '0';
            row.style.transform  = 'translateX(8px)';
            setTimeout(() => row.remove(), 160);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadAllData();
            const select = document.getElementById('letter-type-select');
            if (select) {
                // Show initial form without transition
                const initial = document.getElementById('form-' + select.value);
                if (initial) {
                    document.querySelectorAll('[id^="form-"]').forEach(el => {
                        el.classList.add('hidden');
                        el.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                    });
                    initial.classList.remove('hidden');
                    initial.querySelectorAll('input, select, textarea').forEach(i => i.disabled = false);
                }
            }

            // Hide overlay if page loaded from back/forward cache
            document.getElementById('submit-overlay').classList.remove('active');
            const btn = document.getElementById('submit-btn');
            if (btn) { btn.disabled = false; btn.textContent = 'Buat Dokumen'; }
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