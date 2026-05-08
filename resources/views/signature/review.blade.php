<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Tinjau Permintaan Tanda Tangan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/signature/review.css'])
</head>
<body>
    <div class="review-card fade-up">
        {{-- Header --}}
        <div class="card-header">
            <div
                style="width:48px;height:48px;background:linear-gradient(135deg,var(--gold-500),var(--gold-300));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;">
                <svg style="width:22px;height:22px;color:#0d1526;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </div>
            <h1 style="font-family:var(--font-display);color:#fff;font-size:1.25rem;line-height:1.2;">Permintaan Tanda
                Tangan Digital</h1>
            <p
                style="color:var(--gold-400);font-size:0.72rem;letter-spacing:0.06em;text-transform:uppercase;margin-top:0.3rem;">
                eDokPUPRD — Dinas PUPRD Kota Tomohon</p>
        </div>

        {{-- Body --}}
        <div class="card-body">

            <p style="color:rgba(255,255,255,0.6);font-size:0.82rem;margin-bottom:1rem;line-height:1.5;">
                Yth. <strong
                    style="color:rgba(255,255,255,0.9);">{{ $signatureRequest->official->staff_name }}</strong>,<br>
                Anda diminta untuk meninjau dan menandatangani dokumen berikut:
            </p>

            {{-- Document info --}}
            <div
                style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:1rem;margin-bottom:0.5rem;">
                <div class="info-row">
                    <span class="info-label">Jenis Dokumen</span>
                    <span class="info-value">{{ $signatureRequest->documentLog->documentType->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Diminta oleh</span>
                    <span class="info-value">{{ $signatureRequest->user?->name ?? 'Pengguna' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Permintaan</span>
                    <span
                        class="info-value">{{ $signatureRequest->requested_at->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">File Dokumen</span>
                    <span class="info-value" style="font-family:var(--font-mono);font-size:0.7rem;">
                        {{ $signatureRequest->documentLog->output_filename }}
                    </span>
                </div>
            </div>

            @if (!$signatureRequest->documentFileExists())
                <div class="alert alert-warning"
                    style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.3);border-radius:8px;padding:0.75rem;color:rgba(251,191,36,0.9);font-size:0.78rem;margin-bottom:1rem;">
                    ⚠ File dokumen tidak ditemukan di server (mungkin sudah dihapus otomatis). Anda masih dapat menyetujui
                    atau menolak permintaan ini.
                </div>
            @endif

            {{-- Decision form --}}
            <div class="decision-section">
                <p
                    style="font-size:0.72rem;font-weight:700;letter-spacing:0.07em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:1rem;">
                    Keputusan Anda</p>

                <form method="POST" action="{{ route('signature.process', $signatureRequest->token) }}"
                    id="review-form">
                    @csrf
                    <input type="hidden" name="decision" id="decision-input" value="">

                    <div style="margin-bottom:1rem;">
                        <label
                            style="display:block;font-size:0.78rem;color:rgba(255,255,255,0.55);margin-bottom:0.4rem;">
                            Catatan (opsional)
                        </label>
                        <textarea name="notes" class="notes-area" rows="3"
                            placeholder="Tambahkan catatan jika diperlukan...">{{ old('notes') }}</textarea>
                    </div>

                    @if ($errors->any())
                        <div
                            style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:0.65rem;margin-bottom:1rem;font-size:0.78rem;color:rgba(239,68,68,0.9);">
                            @foreach ($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div style="display:flex;gap:0.75rem;">
                        <button type="button" onclick="submitDecision('approved')" class="btn-approve"
                            style="flex:1;justify-content:center;">
                            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui
                        </button>
                        <button type="button" onclick="submitDecision('rejected')" class="btn-reject"
                            style="flex:1;justify-content:center;">
                            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak
                        </button>
                    </div>
                </form>
            </div>

            <p
                style="font-size:0.7rem;color:rgba(255,255,255,0.25);text-align:center;margin-top:1.25rem;line-height:1.5;">
                Tautan ini hanya dapat digunakan satu kali dan khusus untuk permintaan ini.<br>
                Jika Anda bukan orang yang dituju, abaikan halaman ini.
            </p>
        </div>
    </div>

    <script>
        function submitDecision(decision) {
            const label = decision === 'approved' ? 'menyetujui' : 'menolak';
            if (!confirm(`Apakah Anda yakin ingin ${label} permintaan tanda tangan ini?`)) return;
            document.getElementById('decision-input').value = decision;
            document.getElementById('review-form').submit();
        }
    </script>

</body>

</html>