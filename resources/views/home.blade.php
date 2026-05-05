<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Buat Dokumen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: linear-gradient(155deg, #eef2f8 0%, #f5f7fc 55%, #edf1f7 100%); }

        /* ── Page Header ──────────────────────────────────── */
        .page-hero {
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-800) 60%, #1a2a50 100%);
            border-bottom: 1px solid rgba(201,168,76,0.2);
            padding: 1.5rem 0;
        }

        /* ── Form Card ────────────────────────────────────── */
        .form-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(13,21,38,0.1);
        }

        /* ── Document type tab strip ──────────────────────── */
        .doc-type-select-wrap {
            background: linear-gradient(135deg, rgba(13,21,38,0.03), rgba(42,82,152,0.05));
            border: 1.5px solid var(--navy-100);
            border-radius: 10px;
            padding: 0.85rem;
        }

        /* ── Autofill slot panel ──────────────────────────── */
        .autofill-panel {
            background: linear-gradient(135deg, rgba(42,82,152,0.04), rgba(201,168,76,0.04));
            border: 1.5px solid rgba(42,82,152,0.12);
            border-radius: 10px;
            padding: 0.85rem 1rem;
            position: relative;
            overflow: hidden;
        }
        .autofill-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(180deg, var(--navy-500), var(--gold-400));
            border-radius: 0 2px 2px 0;
        }

        /* ── Loop checklist container ─────────────────────── */
        .loop-container {
            border: 1.5px solid var(--slate-200);
            border-radius: 10px;
            overflow: hidden;
            background: rgba(255,255,255,0.6);
        }
        .loop-header {
            background: linear-gradient(90deg, var(--navy-800), var(--navy-700));
            padding: 0.6rem 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .loop-search-wrap {
            padding: 0.5rem 0.65rem;
            border-bottom: 1px solid var(--slate-200);
            background: rgba(255,255,255,0.5);
        }
        .loop-search {
            width: 100%;
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            padding: 0.45rem 0.75rem;
            font-size: 0.78rem;
            font-family: var(--font-body);
            background: #fff;
            color: var(--slate-700);
            outline: none;
            transition: border-color 0.2s;
        }
        .loop-search:focus { border-color: var(--navy-300); }
        .loop-checklist {
            overflow-y: auto;
            max-height: 220px;
            padding: 0.35rem;
        }
        .loop-footer {
            padding: 0.45rem 0.85rem;
            background: rgba(42,82,152,0.04);
            border-top: 1px solid rgba(42,82,152,0.08);
        }

        /* ── Form section heading ─────────────────────────── */
        .form-section-heading {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--navy-500);
            border-bottom: 1px solid var(--navy-100);
            padding-bottom: 0.4rem;
            margin: 1.25rem 0 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-section-heading::before {
            content: '';
            width: 16px; height: 2px;
            background: linear-gradient(90deg, var(--gold-500), var(--gold-300));
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* ── Submit button ────────────────────────────────── */
        #submit-btn {
            background: linear-gradient(135deg, var(--navy-700), var(--navy-600));
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            letter-spacing: 0.03em;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(31,64,104,0.3);
            font-family: var(--font-body);
        }
        #submit-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--navy-600), var(--navy-500));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(31,64,104,0.4);
        }
        #submit-btn:active:not(:disabled) { transform: translateY(0); }
        #submit-btn:disabled { opacity: 0.65; cursor: not-allowed; }

        /* ── Consent area ─────────────────────────────────── */
        .consent-area {
            background: rgba(42,82,152,0.04);
            border: 1px solid rgba(42,82,152,0.1);
            border-radius: 8px;
            padding: 0.75rem 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        /* ── Row item (repeating group) ───────────────────── */
        .row-item {
            background: rgba(255,255,255,0.6);
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.15s ease;
        }
        .row-item:hover { border-color: var(--navy-200); }

        /* ── Guide panel adjustments ──────────────────────── */
        .guide-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--navy-700);
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .guide-section-title::before {
            content: '';
            width: 12px; height: 2px;
            background: var(--gold-400);
            border-radius: 2px;
        }

        .guide-step {
            display: flex;
            gap: 0.65rem;
            align-items: flex-start;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(42,82,152,0.06);
        }
        .guide-step:last-child { border-bottom: none; }
        .guide-step-num {
            width: 20px; height: 20px;
            border-radius: 50%;
            background: var(--navy-700);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }
        .guide-step-num.done { background: #15803d; }
        .guide-step-title { font-size: 0.78rem; font-weight: 600; color: var(--slate-700); }
        .guide-step-desc  { font-size: 0.72rem; color: var(--slate-500); line-height: 1.45; margin-top: 0.1rem; }

        /* ── Warning / tip box ────────────────────────────── */
        .tip-box {
            border-radius: 8px;
            padding: 0.75rem 0.9rem;
            font-size: 0.75rem;
            line-height: 1.5;
        }
        .tip-box-gold {
            background: rgba(201,168,76,0.08);
            border: 1px solid rgba(201,168,76,0.2);
            color: #7a5f1a;
        }
        .tip-box-navy {
            background: rgba(42,82,152,0.06);
            border: 1px solid rgba(42,82,152,0.15);
            color: var(--navy-700);
        }

        /* ── Download success banner ──────────────────────── */
        .success-banner {
            background: linear-gradient(135deg, rgba(21,128,61,0.08), rgba(42,82,152,0.06));
            border: 1px solid rgba(21,128,61,0.2);
            border-radius: 10px;
            padding: 0.9rem 1.1rem;
        }
        .download-btn {
            background: linear-gradient(135deg, #15803d, #16a34a);
            color: #fff;
            padding: 0.5rem 1.1rem;
            border-radius: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(21,128,61,0.2);
        }
        .download-btn:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(21,128,61,0.3); }
        .preview-btn {
            background: linear-gradient(135deg, var(--navy-600), var(--navy-500));
            color: #fff;
            padding: 0.5rem 1.1rem;
            border-radius: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(31,64,104,0.2);
            font-family: var(--font-body);
        }
        .preview-btn:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(31,64,104,0.3); }

        /* ── Autofill flash ───────────────────────────────── */
        @keyframes autofillFlash {
            0%   { background-color: rgba(201,168,76,0.15); border-color: var(--gold-400) !important; }
            100% { background-color: transparent; border-color: var(--slate-200) !important; }
        }
        .autofill-highlight { animation: autofillFlash 1.3s ease-out forwards; }

        /* ── Loop item ────────────────────────────────────── */
        .loop-item { border: 1px solid transparent; }
        .loop-item.checked-item {
            background: rgba(42,82,152,0.06);
            border-color: rgba(42,82,152,0.15) !important;
        }

        /* ── Mobile ───────────────────────────────────────── */
        @media (max-width: 640px) {
            .row-group-grid { grid-template-columns: 1fr !important; }
            .loop-checklist { max-height: 180px; }
        }
    </style>
</head>

<body>

    {{-- Loading overlay --}}
    <div id="submit-overlay">
        <div class="sipadu-spinner"></div>
        <p>Sedang membuat dokumen...</p>
        <small>Mohon tunggu, jangan tutup halaman ini.</small>
    </div>

    {{-- ── Navbar ──────────────────────────────────────────── --}}
    <nav class="sipadu-nav" x-data="{ menuOpen: false }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div style="width:30px;height:30px;background:linear-gradient(135deg,var(--gold-500),var(--gold-300));border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:14px;height:14px;color:#0d1526;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="hidden sm:block">
                    <div class="nav-brand-title">eDokPUPRD</div>
                    <div class="nav-brand-sub">DINAS PUPRD · Kota Tomohon</div>
                </div>
                <div class="sm:hidden nav-brand-title" style="font-size:0.9rem;">eDokPUPRD</div>
            </div>

            {{-- Desktop nav --}}
            <div class="hidden sm:flex items-center gap-1">
                @auth
                    <span class="sipadu-nav-link">
                        {{ auth()->user()->name }}
                        <span class="badge badge-gold ml-1.5" style="font-size:0.62rem;">{{ ucfirst(auth()->user()->role) }}</span>
                    </span>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="sipadu-nav-link">Admin Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sipadu-nav-link" style="background:none;border:none;cursor:pointer;font-family:var(--font-body);color:rgba(239,68,68,0.65);transition:color 0.2s;" onmouseover="this.style.color='rgba(239,68,68,0.9)'" onmouseout="this.style.color='rgba(239,68,68,0.65)'">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="sipadu-nav-link">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-gold" style="padding:0.35rem 0.9rem;font-size:0.78rem;">Daftar</a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button class="sm:hidden p-2 rounded-lg" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.7);"
                    @click="menuOpen = !menuOpen">
                <svg x-show="!menuOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="menuOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="menuOpen" x-transition class="sm:hidden border-t px-4 py-3 space-y-1" style="border-color:rgba(255,255,255,0.08);background:rgba(13,21,38,0.98);">
            @auth
                <div class="flex items-center gap-2 pb-2 mb-2 border-b" style="border-color:rgba(255,255,255,0.08);">
                    <span style="font-size:0.85rem;color:rgba(255,255,255,0.85);font-weight:500;">{{ auth()->user()->name }}</span>
                    <span class="badge badge-gold">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block sipadu-nav-link">Admin Panel</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left sipadu-nav-link" style="background:none;border:none;cursor:pointer;font-family:var(--font-body);color:rgba(239,68,68,0.7);">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block sipadu-nav-link">Masuk</a>
                <a href="{{ route('register') }}" class="block sipadu-nav-link">Daftar</a>
            @endauth
        </div>
    </nav>

    {{-- ── Page header strip ───────────────────────────────── --}}
    <div class="page-hero">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <div style="width:3px;height:32px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                <div>
                    <h1 style="font-family:var(--font-display);color:#fff;font-size:1.2rem;line-height:1.1;" class="fade-up">
                        Sistem Pembuatan Dokumen Digital
                    </h1>
                    <p style="color:rgba(255,255,255,0.4);font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;margin-top:0.15rem;" class="fade-up fade-up-1">
                        Pilih jenis dokumen dan isi form yang tersedia
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab bar --}}
    <div
        style="background:rgba(255,255,255,0.7);backdrop-filter:blur(12px);border-bottom:1px solid rgba(0,0,0,0.07);sticky top position handled by nav above">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex gap-1 pt-2">
            <button onclick="switchTab('form')" id="tab-form"
                style="padding:0.5rem 1rem;font-size:0.82rem;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid var(--navy-600);color:var(--navy-700);font-family:var(--font-body);">
                Buat Dokumen
            </button>
            @auth
                <button onclick="switchTab('requests')" id="tab-requests"
                    style="padding:0.5rem 1rem;font-size:0.82rem;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:var(--slate-400);font-family:var(--font-body);">
                    Permintaan TTD
                    @if($signatureRequests->where('status', 'pending')->count() > 0)
                        <span
                            style="background:#7c3aed;color:#fff;border-radius:10px;padding:0.1rem 0.45rem;font-size:0.65rem;margin-left:0.3rem;">
                            {{ $signatureRequests->where('status', 'pending')->count() }}
                        </span>
                    @endif
                </button>
                <button onclick="switchTab('history')" id="tab-history"
                    style="padding:0.5rem 1rem;font-size:0.82rem;font-weight:600;border:none;background:none;cursor:pointer;border-bottom:2px solid transparent;color:var(--slate-400);font-family:var(--font-body);">
                    Riwayat Dokumen
                </button>
            @endauth
        </div>
    </div>

    {{-- ── Main content ────────────────────────────────────── --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6">

        {{-- Flash messages --}}
        @if ($errors->any())
            <div class="alert alert-error mb-4 fade-up">
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)<li class="flex items-center gap-1.5"><span>•</span>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="success-banner mb-4 fade-up">
                <div class="flex items-center gap-2 mb-3">
                    <svg style="width:16px;height:16px;color:#15803d;flex-shrink:0;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span style="font-size:0.85rem;font-weight:600;color:#14532d;">{{ session('success') }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if (session('download_url'))
                        <a href="{{ session('download_url') }}" class="download-btn">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Unduh Dokumen
                        </a>
                    @endif

                    @if (session('preview_url'))
                        <button type="button" onclick="openPreview('{{ session('preview_url') }}')" class="preview-btn">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview Dokumen
                        </button>
                    @endif
                    
                    @if (session('signature_log_id') && auth()->check())
                        @php
        $sigLog = \App\Models\DocumentLog::with('documentType')->find(session('signature_log_id'));
                        @endphp
                        @if ($sigLog && $sigLog->documentType->signature_enabled)
                            <a href="{{ route('signature.create', $sigLog) }}"
                                style="background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;padding:0.5rem 1.1rem;border-radius:7px;font-size:0.8rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;transition:all 0.2s ease;box-shadow:0 3px 10px rgba(124,58,237,0.25);"
                                onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 5px 14px rgba(124,58,237,0.35)'"
                                onmouseout="this.style.transform='';this.style.boxShadow='0 3px 10px rgba(124,58,237,0.25)'">
                                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Minta Tanda Tangan
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        @if (session('email_warning'))
            <div class="alert alert-warning mb-4 fade-up"
                style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.3);border-radius:10px;padding:0.85rem 1rem;font-size:0.82rem;color:#854d0e;">
                ⚠ {{ session('email_warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error mb-4 fade-up">{{ session('error') }}</div>
        @endif

        @auth
            @if (auth()->user()->isGuest())
                <div class="alert alert-warning mb-4 fade-up" style="font-size:0.82rem;">
                    Anda login sebagai <strong>Guest</strong>. Hanya dokumen publik yang tersedia.
                    Hubungi admin untuk upgrade akses.
                </div>
            @endif
        @endauth

        <div id="panel-form">
            @if ($documentTypes->isEmpty())
                <div class="form-card p-12 text-center fade-up" style="color:var(--slate-400);">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Tidak ada dokumen tersedia saat ini.
                </div>
            @else

            <div class="flex flex-col lg:flex-row gap-5 items-start fade-up fade-up-2">

                {{-- ── Form Column ──────────────────────────────── --}}
                <div class="flex-1 min-w-0 w-full">
                    <div class="form-card p-5 sm:p-6">
                        <form action="{{ route('document.generate') }}" method="POST" id="main-form">
                            @csrf

                            {{-- Document type selector --}}
                            <div class="doc-type-select-wrap mb-5">
                                <label class="form-label" style="font-size:0.72rem;margin-bottom:0.5rem;">
                                    <span style="color:var(--navy-500);">▸</span> Jenis Surat / Dokumen
                                </label>
                                <select name="letter-type" id="letter-type-select"
                                    onchange="showForm(this.value)" class="w-full">
                                    @foreach ($documentTypes as $type)
                                        <option value="{{ $type->key }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ── Dynamic form sections ────────── --}}
                            @foreach ($documentTypes as $docType)
                                @php
        $fields = $allFields[$docType->id] ?? collect();
        $topFields = $fields->where('is_group_child', false);
        $slots = $docType->slots;
        $autoNumberField = $numberCounters[$docType->id] ?? null;

        $chunks = [];
        foreach ($topFields as $field) {
            if (is_null($field->row_group)) {
                $chunks[] = ['type' => 'single', 'field' => $field];
            } else {
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

                                <div id="form-{{ $docType->key }}" class="{{ !$loop->first ? 'hidden' : '' }}">

                                    {{-- Autofill slots --}}
                                    @foreach ($slots as $slot)
                                        <div class="autofill-panel mb-4">
                                            <p style="font-size:0.74rem;font-weight:700;color:var(--navy-600);margin-bottom:0.6rem;letter-spacing:0.02em;">
                                                Autofill — {{ $slot->slot_label }}
                                            </p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="form-label" style="font-size:0.7rem;color:var(--navy-500);">Dari Data Staff</label>
                                                    <select onchange="fillFromSource('{{ $docType->key }}', '{{ $slot->slot_key }}', 'staff', this.value)"
                                                        class="staff-dropdown w-full" style="border:1.5px solid var(--navy-100);border-radius:7px;padding:0.5rem 0.75rem;font-size:0.8rem;background:#fff;color:var(--slate-700);outline:none;font-family:var(--font-body);">
                                                        <option value="">— Pilih Staff —</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label" style="font-size:0.7rem;color:var(--navy-500);">Dari Data Pejabat</label>
                                                    <select onchange="fillFromSource('{{ $docType->key }}', '{{ $slot->slot_key }}', 'official', this.value)"
                                                        class="official-dropdown w-full" style="border:1.5px solid var(--navy-100);border-radius:7px;padding:0.5rem 0.75rem;font-size:0.8rem;background:#fff;color:var(--slate-700);outline:none;font-family:var(--font-body);">
                                                        <option value="">— Pilih Pejabat —</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Auto-number badge --}}
                                    @if(isset($numberCounters[$docType->id]))
                                        @php $autoField = $numberCounters[$docType->id]; @endphp
                                        <div class="tip-box tip-box-navy mb-3" style="font-size:0.75rem;">
                                            <strong>Nomor Surat Otomatis:</strong>
                                            Field <code class="bg-blue-100 px-1 rounded">{{ $autoField }}</code>
                                            akan diisi otomatis saat dokumen dibuat.
                                        </div>
                                    @endif

                                    {{-- Fields --}}
                                    @php $currentSection = null; @endphp
                                    @foreach ($chunks as $chunk)
                                        @if ($chunk['type'] === 'single')
                                            @php $field = $chunk['field']; @endphp
                                            @if ($field->section_label && $field->section_label !== $currentSection)
                                                @php $currentSection = $field->section_label; @endphp
                                                <h3 class="form-section-heading">{{ $field->section_label }}</h3>
                                            @endif
                                            <div class="mb-3.5">
                                                @include('partials.form-field', ['field' => $field, 'docType' => $docType, 'fields' => $fields, 'autoNumberField' => $autoNumberField,])
                                            </div>
                                        @else
                                            @php $firstField = $chunk['fields'][0]; @endphp
                                            @if ($firstField->section_label && $firstField->section_label !== $currentSection)
                                                @php $currentSection = $firstField->section_label; @endphp
                                                <h3 class="form-section-heading">{{ $firstField->section_label }}</h3>
                                            @endif
                                            <div class="grid gap-3.5 mb-3.5 row-group-grid" style="grid-template-columns: repeat({{ count($chunk['fields']) }}, 1fr)">
                                                @foreach ($chunk['fields'] as $field)
                                                    <div>
                                                        @include('partials.form-field', ['field' => $field, 'docType' => $docType, 'fields' => $fields, 'autoNumberField' => $autoNumberField,])
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach

                                </div>
                            @endforeach

                            {{-- Consent + Submit --}}
                            <div class="mt-6 pt-4" style="border-top:1px solid var(--slate-200);">
                                <div class="consent-area mb-4">
                                    <input type="checkbox" name="consent" id="consent"
                                        style="width:16px;height:16px;border-radius:4px;accent-color:var(--navy-600);flex-shrink:0;margin-top:0.1rem;cursor:pointer;" />
                                    <label for="consent" style="font-size:0.8rem;color:var(--slate-600);cursor:pointer;line-height:1.45;">
                                        Saya menyatakan bahwa informasi yang saya berikan adalah <strong>benar</strong> dan dapat dipertanggungjawabkan.
                                    </label>
                                </div>
                                <button type="button" id="submit-btn" onclick="submitIfConsented()">
                                    Buat Dokumen
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- ── Guide Column ─────────────────────────────── --}}
                <div class="w-full lg:w-72 lg:flex-shrink-0 lg:sticky lg:top-20 space-y-3">

                    {{-- Quick guide --}}
                    <div class="guide-card p-4 fade-up fade-up-2">
                        <div class="guide-section-title">Cara Membuat Dokumen</div>
                        <div class="space-y-0">
                            @foreach([
        ['Pilih Jenis Dokumen', 'Gunakan dropdown untuk memilih template.'],
        ['Gunakan Autofill', 'Pilih nama dari panel autofill untuk isi otomatis.'],
        ['Isi Form', 'Lengkapi semua field wajib (*).'],
        ['Centang Persetujuan', 'Konfirmasi kebenaran data.'],
        ['Klik Buat Dokumen', 'Sistem akan memproses dan menghasilkan file.'],
        ['Unduh / Preview', 'Tombol muncul setelah dokumen berhasil dibuat.'],
    ] as $i => [$t, $d])
                            <div class="guide-step">
                                <div class="guide-step-num {{ $i >= 4 ? 'done' : '' }}">{{ $i + 1 }}</div>
                                <div>
                                    <div class="guide-step-title">{{ $t }}</div>
                                    <div class="guide-step-desc">{{ $d }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Field types --}}
                    <div class="guide-card p-4 fade-up fade-up-3">
                        <div class="guide-section-title">Jenis Field</div>
                        <div class="space-y-1.5">
                            @foreach([
        ['Text / Textarea', 'Ketik teks bebas.'],
        ['Date', 'Pilih dari kalender — format Indonesia.'],
        ['Number', 'Ketik angka.'],
        ['Select', 'Pilih satu dari daftar.'],
        ['Checkbox', 'Centang untuk Ya/Benar.'],
        ['Repeating Group', 'Tambah baris data dinamis.'],
        ['Staff / Pejabat Loop', 'Centang nama, drag ⠿ untuk urutkan.'],
    ] as [$t, $d])
                            <div style="font-size:0.75rem;">
                                <span style="font-weight:600;color:var(--slate-700);">{{ $t }}</span>
                                <span style="color:var(--slate-400);"> — {{ $d }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div class="tip-box tip-box-navy fade-up fade-up-4">
                        <div class="guide-section-title" style="color:var(--navy-600);margin-bottom:0.5rem;">Tips Autofill</div>
                        <ul class="space-y-1" style="font-size:0.73rem;">
                            <li>• Setiap slot memiliki <strong>dua sumber</strong>: Data Staff dan Data Pejabat.</li>
                            <li>• Field yang terisi otomatis masih <strong>bisa diedit manual</strong>.</li>
                            <li>• Drag <strong>⠿</strong> untuk mengubah urutan peserta dalam dokumen.</li>
                        </ul>
                    </div>

                    {{-- Warning --}}
                    <div class="tip-box tip-box-gold fade-up fade-up-4">
                        <div class="guide-section-title" style="color:#7a5f1a;margin-bottom:0.5rem;">Perhatian</div>
                        <ul class="space-y-1" style="font-size:0.73rem;">
                            <li>• File otomatis <strong>dihapus</strong> dari server setelah beberapa menit.</li>
                            <li>• Segera unduh setelah dokumen berhasil dibuat.</li>
                        </ul>
                    </div>

                </div>

            </div>
            @endif
        </div>

        {{-- Requests panel --}}
        @auth
        <div id="panel-requests" style="display:none;">
            @if ($signatureRequests->isEmpty())
                <div class="form-card p-12 text-center fade-up" style="color:var(--slate-400);">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Belum ada permintaan tanda tangan.
                </div>
            @else
                <div class="form-card overflow-hidden fade-up">
                    <table class="w-full text-sm">
                        <thead style="background:linear-gradient(90deg,var(--navy-800),var(--navy-700));">
                            <tr>
                                <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Dokumen</th>
                                <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Pejabat</th>
                                <th style="padding:0.75rem 1rem;text-align:center;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Status</th>
                                <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Diminta</th>
                                <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Ditinjau</th>
                                <th style="padding:0.75rem 1rem;text-align:center;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($signatureRequests as $req)
                            <tr style="border-bottom:1px solid var(--slate-200);transition:background 0.15s;"
                                onmouseover="this.style.background='rgba(42,82,152,0.03)'"
                                onmouseout="this.style.background=''">
                                <td style="padding:0.75rem 1rem;">
                                    <p style="font-weight:600;color:var(--navy-800);font-size:0.83rem;">{{ $req->documentLog->documentType->name }}</p>
                                    <p style="font-family:var(--font-mono);font-size:0.68rem;color:var(--slate-400);margin-top:0.1rem;">{{ $req->documentLog->output_filename }}</p>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <p style="font-size:0.83rem;color:var(--slate-700);font-weight:500;">{{ $req->official?->staff_name ?? '—' }}</p>
                                    <p style="font-size:0.72rem;color:var(--slate-400);margin-top:0.1rem;">{{ $req->official?->position ?? '' }}</p>
                                </td>
                                <td style="padding:0.75rem 1rem;text-align:center;">
                                    @if ($req->status === 'pending')
                                        <span style="background:#fef9c3;color:#854d0e;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">Menunggu</span>
                                    @elseif ($req->status === 'approved')
                                        <span style="background:#dcfce7;color:#15803d;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">✓ Disetujui</span>
                                    @else
                                        <span style="background:#fee2e2;color:#b91c1c;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">✕ Ditolak</span>
                                    @endif
                                </td>
                                <td style="padding:0.75rem 1rem;font-size:0.78rem;color:var(--slate-500);">
                                    {{ $req->requested_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '—' }}
                                </td>
                                <td style="padding:0.75rem 1rem;font-size:0.78rem;color:var(--slate-500);">
                                    {{ $req->reviewed_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '—' }}
                                    @if ($req->notes)
                                        <p style="font-size:0.7rem;color:var(--slate-400);font-style:italic;margin-top:0.15rem;">"{{ Str::limit($req->notes, 40) }}"</p>
                                    @endif
                                </td>
                                <td style="padding:0.75rem 1rem;text-align:center;">
                                    <div class="flex flex-col gap-1 items-center">
                                        <a href="{{ route('signature.verify', $req->token) }}"
                                            style="font-size:0.75rem;color:var(--navy-600);text-decoration:none;font-weight:500;padding:0.3rem 0.7rem;border:1px solid var(--navy-200);border-radius:6px;transition:all 0.15s;"
                                            onmouseover="this.style.background='var(--navy-100)'"
                                            onmouseout="this.style.background=''">
                                            Detail
                                        </a>
                                        @if ($req->isPending())
                                            <form method="POST" action="{{ route('signature.resend', $req) }}">
                                                @csrf
                                                <button type="submit"
                                                    style="font-size:0.72rem;color:var(--slate-500);padding:0.25rem 0.6rem;border:1px solid var(--slate-200);border-radius:6px;background:transparent;cursor:pointer;font-family:var(--font-body);margin-top:0.2rem;transition:all 0.15s;"
                                                    onmouseover="this.style.background='var(--slate-100)'"
                                                    onmouseout="this.style.background='transparent'">
                                                    ↺ Kirim Ulang Email
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div id="panel-history" style="display:none;">
    @if ($documentHistory->isEmpty())
        <div class="form-card p-12 text-center fade-up" style="color:var(--slate-400);">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Belum ada riwayat dokumen.
        </div>
    @else
        <div class="form-card overflow-hidden fade-up">
            <table class="w-full text-sm">
                <thead style="background:linear-gradient(90deg,var(--navy-800),var(--navy-700));">
                    <tr>
                        <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Jenis Dokumen</th>
                        <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">File</th>
                        <th style="padding:0.75rem 1rem;text-align:center;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Status</th>
                        <th style="padding:0.75rem 1rem;text-align:center;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">TTD</th>
                        <th style="padding:0.75rem 1rem;text-align:left;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Dibuat</th>
                        <th style="padding:0.75rem 1rem;text-align:center;color:rgba(255,255,255,0.85);font-size:0.72rem;letter-spacing:0.05em;font-weight:600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentHistory as $log)
                    @php
                        $sigReq = $log->signatureRequests->sortByDesc('requested_at')->first();
                        $fileExists = file_exists(public_path('cached_result/' . $log->output_filename));
                        $signedExists = $sigReq?->signed_filename && file_exists(public_path('cached_result/' . $sigReq->signed_filename));
                    @endphp
                    <tr style="border-bottom:1px solid var(--slate-200);transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(42,82,152,0.03)'"
                        onmouseout="this.style.background=''">
                        <td style="padding:0.75rem 1rem;">
                            <p style="font-weight:600;color:var(--navy-800);font-size:0.83rem;">{{ $log->documentType->name }}</p>
                        </td>
                        <td style="padding:0.75rem 1rem;">
                            <p style="font-family:var(--font-mono);font-size:0.68rem;color:var(--slate-400);">{{ $log->output_filename }}</p>
                            @if (!$fileExists && !$signedExists)
                                <p style="font-size:0.68rem;color:#b91c1c;margin-top:0.15rem;">⚠ File dihapus</p>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem;text-align:center;">
                            @if ($log->status === 'success')
                                <span style="background:#dcfce7;color:#15803d;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">Berhasil</span>
                            @else
                                <span style="background:#fee2e2;color:#b91c1c;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">Gagal</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem;text-align:center;">
                            @if (!$sigReq)
                                <span style="color:var(--slate-300);font-size:0.72rem;">—</span>
                            @elseif ($sigReq->status === 'approved')
                                <span style="background:#dcfce7;color:#15803d;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">Ditandatangani</span>
                            @elseif ($sigReq->status === 'pending')
                                <span style="background:#fef9c3;color:#854d0e;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">Menunggu</span>
                            @else
                                <span style="background:#fee2e2;color:#b91c1c;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.72rem;font-weight:600;">Ditolak</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem;font-size:0.78rem;color:var(--slate-500);">
                            {{ $log->generated_at->locale('id')->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td style="padding:0.75rem 1rem;text-align:center;">
                            <div style="display:flex;flex-direction:column;gap:0.3rem;align-items:center;">
                                @if ($signedExists)
                                    <a href="{{ route('document.download', $sigReq->signed_filename) }}"
                                        style="font-size:0.75rem;color:#fff;background:linear-gradient(135deg,#7c3aed,#6d28d9);padding:0.3rem 0.75rem;border-radius:6px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.3rem;">
                                        ⬇ Bertanda Tangan
                                    </a>
                                @endif
                                @if ($fileExists)
                                    <a href="{{ route('document.download', $log->output_filename) }}"
                                        style="font-size:0.75rem;color:#fff;background:linear-gradient(135deg,#15803d,#16a34a);padding:0.3rem 0.75rem;border-radius:6px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:0.3rem;">
                                        ⬇ Unduh
                                    </a>
                                @elseif (!$signedExists)
                                    <button onclick="showToast('File telah dihapus dari server.', 'error')"
                                        style="font-size:0.75rem;color:var(--slate-400);background:transparent;border:1px solid var(--slate-200);padding:0.3rem 0.75rem;border-radius:6px;cursor:pointer;font-family:var(--font-body);">
                                        Tidak Tersedia
                                    </button>
                                @endif
                                @if ($log->documentType->signature_enabled && !$sigReq && $fileExists)
                                    <a href="{{ route('signature.create', $log) }}"
                                        style="font-size:0.72rem;color:var(--navy-600);padding:0.25rem 0.6rem;border:1px solid var(--navy-200);border-radius:6px;text-decoration:none;transition:all 0.15s;"
                                        onmouseover="this.style.background='var(--navy-100)'"
                                        onmouseout="this.style.background=''">
                                        Minta TTD
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
        @endauth
    </main>

    {{-- ── Preview Modal ───────────────────────────────────── --}}
    <div id="preview-modal"
        class="sipadu-modal-bg"
        style="display: none;"
        onclick="if(event.target===this) closePreview()">
        <div class="sipadu-modal w-full max-w-4xl mx-4 flex flex-col" style="height:90vh;">

            <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--slate-200);">
                <div style="display:flex;align-items:center;gap:0.6rem;">
                    <div style="width:3px;height:20px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                    <h2 style="font-size:0.95rem;font-weight:700;color:var(--navy-800);">Preview Dokumen</h2>
                </div>
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <a id="preview-download-btn" href="{{ session('download_url') }}" class="download-btn hidden" style="padding:0.4rem 0.9rem;">
                        ⬇ Unduh
                    </a>
                    <button onclick="closePreview()"
                        style="width:28px;height:28px;border-radius:6px;border:1px solid var(--slate-200);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-400);font-size:1rem;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--slate-100)';this.style.color='var(--slate-700)'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--slate-400)'">✕</button>
                </div>
            </div>

            <div id="preview-loading" class="flex-1 flex items-center justify-center" style="color:var(--slate-400);">
                <div class="text-center">
                    <div class="sipadu-spinner mx-auto mb-3"></div>
                    <p style="font-size:0.85rem;font-weight:500;">Memuat preview...</p>
                    <p style="font-size:0.72rem;color:var(--slate-300);margin-top:0.25rem;">Konversi PDF memerlukan beberapa detik</p>
                </div>
            </div>
            <div id="preview-error" class="flex-1 items-center justify-center hidden" style="color:#b91c1c;">
                <div class="text-center">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">⚠️</div>
                    <p style="font-size:0.85rem;font-weight:600;">Gagal memuat preview</p>
                    <p style="font-size:0.75rem;color:var(--slate-400);margin-top:0.25rem;" id="preview-error-msg"></p>
                </div>
            </div>
            <iframe id="preview-iframe" class="flex-1 w-full hidden rounded-b-2xl" src="" title="Document Preview"></iframe>
        </div>
    </div>

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

        let staffData = [], officialData = [];

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
            } catch (e) { console.warn('Could not load autofill data:', e); }
        }

        function populateDropdowns() {
            document.querySelectorAll('.staff-dropdown').forEach(function(select) {
                const ph = select.options[0];
                select.innerHTML = '';
                select.appendChild(ph);
                staffData.forEach(function(p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.staff_name + (p.nip ? ' — ' + p.nip : '');
                    select.appendChild(opt);
                });
            });
            document.querySelectorAll('.official-dropdown').forEach(function(select) {
                const ph = select.options[0];
                select.innerHTML = '';
                select.appendChild(ph);
                officialData.forEach(function(p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.staff_name + (p.nip ? ' — ' + p.nip : '');
                    select.appendChild(opt);
                });
            });
        }

        function populateLoopLists() {
            document.querySelectorAll('[data-loop-type]').forEach(function(container) {
                const loopType = container.dataset.loopType;
                const fieldKey = container.dataset.fieldKey;
                const dataset  = loopType === 'staff' ? staffData : officialData;
                const listEl   = container.querySelector('.loop-checklist');
                const searchEl = container.querySelector('.loop-search');
                if (!listEl) return;
                listEl.innerHTML = '';
                dataset.forEach(function(person) {
                    listEl.appendChild(makeLoopItem(person, fieldKey));
                });
                if (searchEl) {
                    searchEl.addEventListener('input', function() {
                        const q = this.value.toLowerCase();
                        listEl.querySelectorAll('.loop-item').forEach(function(item) {
                            item.style.display = item.dataset.name.toLowerCase().includes(q) ? '' : 'none';
                        });
                    });
                }
                initLoopDrag(listEl, fieldKey);
            });
        }

        function makeLoopItem(person, fieldKey) {
            const div = document.createElement('div');
            div.className = 'loop-item flex items-center gap-2 px-3 py-2 rounded';
            div.dataset.id   = person.id;
            div.dataset.name = person.staff_name;
            div.setAttribute('draggable', true);

            const cb = document.createElement('input');
            cb.type      = 'checkbox';
            cb.name      = `field_${fieldKey}[]`;
            cb.value     = person.id;
            cb.style.cssText = 'width:15px;height:15px;accent-color:var(--navy-600);flex-shrink:0;cursor:pointer;';
            cb.addEventListener('change', function() {
                div.classList.toggle('checked-item', this.checked);
                updateLoopCount(div.closest('[data-loop-type]'));
            });

            const label = document.createElement('span');
            label.style.cssText = 'font-size:0.8rem;color:var(--slate-700);flex:1;cursor:pointer;user-select:none;';
            label.textContent = person.staff_name
                + (person.nip      ? ' — ' + person.nip      : '')
                + (person.position ? ' (' + person.position + ')' : '');
            label.addEventListener('click', function() { cb.click(); });

            const handle = document.createElement('span');
            handle.style.cssText = 'color:var(--slate-300);font-size:1rem;user-select:none;cursor:grab;flex-shrink:0;';
            handle.textContent = '⠿';

            div.appendChild(cb);
            div.appendChild(label);
            div.appendChild(handle);
            return div;
        }

        function updateLoopCount(container) {
            if (!container) return;
            const countEl = container.querySelector('.loop-count');
            const checked = container.querySelectorAll('.loop-item input[type="checkbox"]:checked').length;
            if (!countEl) return;
            if (checked === 0) { countEl.textContent = ''; countEl.classList.add('hidden'); }
            else { countEl.textContent = checked + ' dipilih'; countEl.classList.remove('hidden'); }
        }

        function initLoopDrag(listEl) {
            let dragging = null;
            listEl.addEventListener('dragstart', function(e) {
                dragging = e.target.closest('.loop-item');
                if (dragging) dragging.classList.add('dragging');
            });
            listEl.addEventListener('dragend', function() {
                if (dragging) dragging.classList.remove('dragging');
                dragging = null;
            });
            listEl.addEventListener('dragover', function(e) {
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
                if (!fieldConfig || fieldConfig.role !== slotKey) return;
                const input = wrapper.querySelector('input, select, textarea');
                if (input && person[fieldConfig.col] !== undefined && person[fieldConfig.col] !== null) {
                    input.value = person[fieldConfig.col];
                    input.classList.remove('autofill-highlight');
                    void input.offsetWidth;
                    input.classList.add('autofill-highlight');
                    setTimeout(() => input.classList.remove('autofill-highlight'), 1400);
                    filledCount++;
                }
            });
            if (filledCount > 0) showToast(`${filledCount} field terisi dari data ${source === 'staff' ? 'staff' : 'pejabat'}.`);
        }

        let toastTimer = null;
        function showToast(message, type = 'success') {
            let toast = document.getElementById('toast-notification');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast-notification';
                toast.style.cssText = 'position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%) translateY(8px);z-index:9999;transition:all 0.3s ease;opacity:0;';
                document.body.appendChild(toast);
            }
            toast.textContent = message;
            toast.style.background = type === 'success' ? 'var(--navy-700)' : '#dc2626';
            toast.className = 'badge';
            toast.style.cssText += 'padding:0.6rem 1.1rem;border-radius:8px;font-size:0.8rem;font-weight:500;color:#fff;box-shadow:0 4px 16px rgba(0,0,0,0.25);';
            requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(-50%) translateY(0)'; });
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(-50%) translateY(8px)'; }, 2500);
        }

        function showForm(selectedKey) {
            document.querySelectorAll('[id^="form-"]').forEach(function(el) {
                if (!el.classList.contains('hidden')) {
                    el.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
                    el.style.opacity = '0'; el.style.transform = 'translateY(4px)';
                    setTimeout(() => {
                        el.classList.add('hidden');
                        el.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                        el.style.opacity = ''; el.style.transform = '';
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
                    target.style.opacity = '0'; target.style.transform = 'translateY(8px)';
                    target.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                    requestAnimationFrame(() => { target.style.opacity = '1'; target.style.transform = 'translateY(0)'; });
                    setTimeout(() => { target.style.transition = ''; target.style.opacity = ''; target.style.transform = ''; }, 230);
                }
            }, 160);
        }

        function submitIfConsented() {
            if (!document.getElementById('consent').checked) {
                showToast('Centang pernyataan persetujuan terlebih dahulu.', 'error');
                const ca = document.getElementById('consent').closest('.consent-area');
                const shakes = [6, -6, 4, -4, 2, -2, 0];
                shakes.forEach((x, i) => setTimeout(() => ca.style.transform = `translateX(${x}px)`, i * 55));
                setTimeout(() => ca.style.transform = '', shakes.length * 55);
                return;
            }
            document.querySelectorAll('[id^="form-"]').forEach(function(section) {
                if (section.classList.contains('hidden')) {
                    section.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                }
            });
            const btn = document.getElementById('submit-btn');
            if (btn) { btn.disabled = true; btn.textContent = 'Membuat dokumen...'; }
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
            container.appendChild(clone);
            const newRow = container.lastElementChild;
            if (newRow) {
                newRow.style.opacity = '0'; newRow.style.transform = 'translateY(-4px)';
                newRow.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                requestAnimationFrame(() => { newRow.style.opacity = '1'; newRow.style.transform = 'translateY(0)'; });
            }
        }

        function removeRow(btn) {
            const row = btn.closest('.row-item');
            if (!row) return;
            row.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
            row.style.opacity = '0'; row.style.transform = 'translateX(6px)';
            setTimeout(() => row.remove(), 160);
        }

        function openPreview(previewUrl) {
            const modal   = document.getElementById('preview-modal');
            const iframe  = document.getElementById('preview-iframe');
            const loading = document.getElementById('preview-loading');
            const error   = document.getElementById('preview-error');
            iframe.classList.add('hidden');
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            iframe.src = '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            iframe.onload = function() { loading.classList.add('hidden'); iframe.classList.remove('hidden'); };
            iframe.onerror = function() {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('preview-error-msg').textContent = 'Pastikan LibreOffice terinstall di server.';
            };
            iframe.src = previewUrl;
        }

        function closePreview() {
            document.getElementById('preview-modal').style.display = 'none';
            document.getElementById('preview-iframe').src = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePreview(); });

        function switchTab(tab) {
            const panels = { form: 'panel-form', requests: 'panel-requests', history: 'panel-history' };
            const tabs   = { form: 'tab-form',   requests: 'tab-requests',   history: 'tab-history'   };

            Object.keys(panels).forEach(function(key) {
                const panel = document.getElementById(panels[key]);
                const tabEl = document.getElementById(tabs[key]);
                if (panel) panel.style.display = key === tab ? '' : 'none';
                if (tabEl) {
                    tabEl.style.borderBottomColor = key === tab ? 'var(--navy-600)' : 'transparent';
                    tabEl.style.color = key === tab ? 'var(--navy-700)' : 'var(--slate-400)';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadAllData();
            const select = document.getElementById('letter-type-select');
            if (select) {
                document.querySelectorAll('[id^="form-"]').forEach(el => {
                    el.classList.add('hidden');
                    el.querySelectorAll('input, select, textarea').forEach(i => i.disabled = true);
                });
                const initial = document.getElementById('form-' + select.value);
                if (initial) {
                    initial.classList.remove('hidden');
                    initial.querySelectorAll('input, select, textarea').forEach(i => i.disabled = false);
                }
            }
            document.getElementById('submit-overlay').classList.remove('active');
            const btn = document.getElementById('submit-btn');
            if (btn) { btn.disabled = false; btn.textContent = 'Buat Dokumen'; }
        });

        // auto-switch to requests tab if redirected after a signature action
        @if (session('switch_tab') === 'requests')
            document.addEventListener('DOMContentLoaded', () => switchTab('requests'));
        @endif
    </script>

</body>
</html>