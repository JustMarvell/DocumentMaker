<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Verifikasi Dokumen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/signature/verify.css'])
</head>
<body>

    <div class="verify-card fade-up">

        {{-- Header --}}
        <div class="card-header">
            @if ($signatureRequest->isApproved())
                <div style="width:52px;height:52px;background:linear-gradient(135deg,#15803d,#16a34a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 0.85rem;box-shadow:0 0 0 6px rgba(21,128,61,0.12);">
                    <svg style="width:24px;height:24px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 style="font-family:var(--font-display);color:#4ade80;font-size:1.2rem;margin-bottom:0.3rem;">Dokumen Terverifikasi</h1>
                <p style="color:rgba(255,255,255,0.45);font-size:0.78rem;">Dokumen ini asli dan telah ditandatangani secara digital</p>
            @elseif ($signatureRequest->isRejected())
                <div style="width:52px;height:52px;background:rgba(239,68,68,0.15);border:2px solid rgba(239,68,68,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 0.85rem;">
                    <svg style="width:24px;height:24px;color:rgb(239,68,68);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h1 style="font-family:var(--font-display);color:#f87171;font-size:1.2rem;margin-bottom:0.3rem;">Permintaan Ditolak</h1>
                <p style="color:rgba(255,255,255,0.45);font-size:0.78rem;">Dokumen ini tidak mendapatkan persetujuan tanda tangan</p>
            @else
                <div style="width:52px;height:52px;background:rgba(251,191,36,0.12);border:2px solid rgba(251,191,36,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 0.85rem;">
                    <svg style="width:24px;height:24px;color:rgb(251,191,36);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 style="font-family:var(--font-display);color:rgb(251,191,36);font-size:1.2rem;margin-bottom:0.3rem;">Menunggu Tanda Tangan</h1>
                <p style="color:rgba(255,255,255,0.45);font-size:0.78rem;">Permintaan tanda tangan masih dalam proses</p>
            @endif

            <p style="color:var(--gold-400);font-size:0.65rem;letter-spacing:0.07em;text-transform:uppercase;margin-top:0.5rem;">
                {{ config('app.name') }} — Dinas PUPRD Kota Tomohon
            </p>
        </div>

        {{-- Document details --}}
        <div style="padding:1.25rem 1.5rem;">

            <p style="font-size:0.65rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.3);margin-bottom:0.85rem;">
                Detail Dokumen
            </p>

            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:0.85rem 1rem;margin-bottom:1rem;">
                <div class="info-row">
                    <span class="info-label">Jenis Dokumen</span>
                    <span class="info-value">{{ $signatureRequest->documentLog->documentType->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dibuat oleh</span>
                    <span class="info-value">{{ $signatureRequest->user?->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pembuatan</span>
                    <span class="info-value">
                        {{ $signatureRequest->documentLog->generated_at->locale('id')->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            @if ($signatureRequest->isApproved() || $signatureRequest->isRejected())
                <p style="font-size:0.65rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.3);margin-bottom:0.85rem;">
                    Detail Tanda Tangan
                </p>
                <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07);border-radius:10px;padding:0.85rem 1rem;margin-bottom:1rem;">
                    <div class="info-row">
                        <span class="info-label">Pejabat</span>
                        <span class="info-value">{{ $signatureRequest->official?->staff_name ?? '—' }}</span>
                    </div>
                    @if ($signatureRequest->official?->position)
                        <div class="info-row">
                            <span class="info-label">Jabatan</span>
                            <span class="info-value">{{ $signatureRequest->official->position }}</span>
                        </div>
                    @endif
                    <!-- @if ($signatureRequest->official?->nip)
                        <div class="info-row">
                            <span class="info-label">NIP</span>
                            <span class="info-value" style="font-family:var(--font-mono);font-size:0.75rem;">
                                {{ $signatureRequest->official->nip }}
                            </span>
                        </div>
                    @endif -->
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value" style="color:{{ $signatureRequest->isApproved() ? '#4ade80' : '#f87171' }};">
                            {{ $signatureRequest->isApproved() ? '✓ Disetujui' : '✕ Ditolak' }}
                        </span>
                    </div>
                    @if ($signatureRequest->reviewed_at)
                        <div class="info-row">
                            <span class="info-label">Tanggal Ditinjau</span>
                            <span class="info-value">
                                {{ $signatureRequest->reviewed_at->locale('id')->translatedFormat('d F Y, H:i') }}
                            </span>
                        </div>
                    @endif
                    @if ($signatureRequest->notes)
                        <div class="info-row">
                            <span class="info-label">Catatan</span>
                            <span class="info-value">{{ $signatureRequest->notes }}</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Verification token strip --}}
            <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:8px;padding:0.75rem;text-align:center;">
                <p style="font-size:0.62rem;color:rgba(255,255,255,0.25);margin-bottom:0.3rem;letter-spacing:0.04em;text-transform:uppercase;">Token Verifikasi</p>
                <p style="font-family:var(--font-mono);font-size:0.65rem;color:rgba(255,255,255,0.35);word-break:break-all;">
                    {{ $signatureRequest->token }}
                </p>
            </div>

            <p style="font-size:0.68rem;color:rgba(255,255,255,0.2);text-align:center;margin-top:1rem;line-height:1.5;">
                Halaman ini dapat diakses oleh siapapun yang memindai QR code pada dokumen.<br>
                Verifikasi dilakukan oleh sistem eDokPUPRD — Dinas PUPRD Kota Tomohon.
            </p>
        </div>
    </div>

</body>
</html>