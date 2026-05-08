<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Minta Tanda Tangan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/signature/create.css'])
</head>
<body>

    {{-- Loading overlay --}}
    <div id="sig-overlay">
        <div class="sipadu-spinner"></div>
        <p>Mengirim permintaan tanda tangan...</p>
        <small>Mohon tunggu, email sedang dikirim ke pejabat.</small>
    </div>

    {{-- Navbar --}}
    <nav class="sipadu-nav">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div style="width:30px;height:30px;background:linear-gradient(135deg,var(--gold-500),var(--gold-300));border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg style="width:14px;height:14px;color:#0d1526;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="nav-brand-title" style="font-size:0.9rem;">eDokPUPRD</div>
            </div>
            <a href="{{ route('home') }}" class="sipadu-nav-link" style="font-size:0.78rem;">← Kembali ke Aplikasi</a>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

        <div class="mb-6 fade-up">
            <div class="flex items-center gap-2 mb-1">
                <div style="width:3px;height:20px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                <span class="section-label">Tanda Tangan Digital</span>
            </div>
            <h1 class="display-heading" style="font-size:1.5rem;color:var(--navy-900);">
                Kirim Permintaan Tanda Tangan
            </h1>
            <p style="color:var(--slate-500);font-size:0.85rem;margin-top:0.3rem;">
                Pilih pejabat yang akan menandatangani dokumen ini.
                Pejabat akan menerima email dengan dokumen terlampir dan tautan persetujuan.
            </p>
        </div>

        {{-- Document info card --}}
        <div class="fade-up fade-up-1" style="background:rgba(42,82,152,0.05);border:1.5px solid rgba(42,82,152,0.12);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:0;left:0;width:3px;height:100%;background:linear-gradient(180deg,var(--navy-500),var(--gold-400));border-radius:0 2px 2px 0;"></div>
            <p style="font-size:0.7rem;font-weight:700;letter-spacing:0.07em;text-transform:uppercase;color:var(--navy-500);margin-bottom:0.6rem;">Dokumen yang Dimintakan TTD</p>
            <div class="grid grid-cols-2 gap-3" style="font-size:0.82rem;">
                <div>
                    <span style="color:var(--slate-400);">Jenis Dokumen</span>
                    <p style="font-weight:600;color:var(--navy-800);margin-top:0.1rem;">{{ $documentLog->documentType->name }}</p>
                </div>
                <div>
                    <span style="color:var(--slate-400);">File</span>
                    <p style="font-family:var(--font-mono);font-size:0.75rem;color:var(--slate-600);margin-top:0.1rem;">{{ $documentLog->output_filename }}</p>
                </div>
                <div>
                    <span style="color:var(--slate-400);">Dibuat pada</span>
                    <p style="font-weight:500;color:var(--slate-700);margin-top:0.1rem;">{{ $documentLog->generated_at->locale('id')->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <div>
                    <span style="color:var(--slate-400);">Status Dokumen</span>
                    <p style="margin-top:0.1rem;">
                        <span class="badge badge-green">{{ ucfirst($documentLog->status) }}</span>
                    </p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-error mb-4 fade-up">
                @foreach ($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="fade-up fade-up-2" style="background:rgba(255,255,255,0.9);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.7);border-radius:16px;box-shadow:0 4px 24px rgba(13,21,38,0.08);padding:1.75rem;">

            <form method="POST" action="{{ route('signature.store', $documentLog) }}" id="sig-form">
                @csrf

                <div class="mb-5">
                    <label class="form-label" style="margin-bottom:0.6rem;">
                        Pilih Pejabat Penandatangan
                        <span style="color:#dc2626;"> *</span>
                    </label>

                    @if ($officials->isEmpty())
                        <div class="alert alert-warning">
                            Belum ada data pejabat. Minta administrator untuk menambahkan data pejabat terlebih dahulu.
                        </div>
                    @else
                        <div style="border:1.5px solid var(--slate-200);border-radius:10px;overflow:hidden;max-height:320px;overflow-y:auto;">
                            @foreach ($officials as $official)
                                <label style="display:flex;align-items:center;gap:1rem;padding:0.85rem 1rem;cursor:pointer;border-bottom:1px solid var(--slate-100);transition:background 0.15s;"
                                       onmouseover="this.style.background='rgba(42,82,152,0.04)'"
                                       onmouseout="this.style.background='transparent'">
                                    <input type="radio"
                                           name="official_id"
                                           value="{{ $official->id }}"
                                           style="width:16px;height:16px;accent-color:var(--navy-600);flex-shrink:0;"
                                           {{ old('official_id') == $official->id ? 'checked' : '' }} />
                                    <div style="flex:1;min-width:0;">
                                        <p style="font-weight:600;color:var(--navy-800);font-size:0.85rem;">{{ $official->staff_name }}</p>
                                        <p style="font-size:0.74rem;color:var(--slate-500);margin-top:0.1rem;">
                                            {{ $official->position ?? '—' }}
                                            @if ($official->work_unit)
                                                · {{ $official->work_unit }}
                                            @endif
                                        </p>
                                    </div>
                                    <div style="text-align:right;flex-shrink:0;">
                                        <p style="font-family:var(--font-mono);font-size:0.7rem;color:var(--slate-400);">{{ $official->nip }}</p>
                                        @if ($official->rank)
                                            <p style="font-size:0.7rem;color:var(--slate-400);margin-top:0.1rem;">{{ $official->rank }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div style="background:rgba(201,168,76,0.07);border:1px solid rgba(201,168,76,0.2);border-radius:8px;padding:0.85rem 1rem;margin-bottom:1.5rem;font-size:0.78rem;color:#7a5f1a;line-height:1.55;">
                    <strong>Yang akan terjadi setelah Anda mengirim:</strong>
                    <ol style="list-style:decimal;padding-left:1.2rem;margin-top:0.4rem;">
                        <li>Email dikirim ke pejabat yang dipilih beserta dokumen terlampir.</li>
                        <li>Pejabat membuka tautan di email untuk meninjau dan memutuskan.</li>
                        <li>Anda akan menerima notifikasi email setelah keputusan dibuat.</li>
                    </ol>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                        id="sig-submit-btn"
                        onclick="submitSigForm()"
                        {{ $officials->isEmpty() ? 'disabled' : '' }}
                        style="flex:1;background:linear-gradient(135deg,var(--navy-700),var(--navy-600));color:#fff;font-weight:700;font-size:0.88rem;padding:0.75rem 1.5rem;border-radius:10px;border:none;cursor:pointer;transition:all 0.25s;box-shadow:0 4px 16px rgba(31,64,104,0.3);font-family:var(--font-body);display:flex;align-items:center;justify-content:center;gap:0.5rem;"
                        onmouseover="if(!this.disabled){this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(31,64,104,0.4)';}"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 4px 16px rgba(31,64,104,0.3)';">
                        <svg id="sig-btn-icon" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span id="sig-btn-text">Kirim Permintaan Tanda Tangan</span>
                    </button>
                    <a href="{{ route('home') }}"
                       style="padding:0.75rem 1.25rem;border-radius:10px;border:1.5px solid var(--slate-200);background:transparent;color:var(--slate-600);font-size:0.85rem;font-weight:500;text-decoration:none;display:flex;align-items:center;transition:all 0.2s;"
                       onmouseover="this.style.background='var(--slate-100)'"
                       onmouseout="this.style.background='transparent'">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </main>

    <script>
    function submitSigForm() {
        const radio = document.querySelector('#sig-form input[name="official_id"]:checked');
        if (!radio) {
            // Shake the list to hint the user
            const list = document.querySelector('#sig-form [style*="max-height:320px"]');
            if (list) {
                const shakes = [6, -6, 4, -4, 2, 0];
                shakes.forEach(function(x, i) {
                    setTimeout(function() { list.style.transform = 'translateX(' + x + 'px)'; }, i * 55);
                });
                setTimeout(function() { list.style.transform = ''; }, shakes.length * 55);
            }
            return;
        }

        const btn  = document.getElementById('sig-submit-btn');
        const text = document.getElementById('sig-btn-text');
        const icon = document.getElementById('sig-btn-icon');

        btn.disabled = true;
        btn.style.opacity = '0.8';
        btn.style.cursor  = 'not-allowed';

        // Swap icon to a small spinner SVG
        icon.outerHTML = '<svg id="sig-btn-icon" style="width:15px;height:15px;animation:spin 0.75s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z"/></svg>';
        text.textContent = 'Mengirim...';

        document.getElementById('sig-overlay').classList.add('active');
        document.getElementById('sig-form').submit();
    }
    </script>

</body>
</html>