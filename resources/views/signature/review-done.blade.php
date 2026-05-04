<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tinjauan Selesai — eDokPUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background:
                radial-gradient(ellipse 80% 60% at 50% -5%, rgba(42, 82, 152, 0.25) 0%, transparent 60%),
                linear-gradient(160deg, #0a0f1e 0%, #0d1526 60%, #101c38 100%);
            min-height: 100vh;
            font-family: var(--font-body);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
    </style>
</head>

<body>

    <div class="fade-up"
        style="width:100%;max-width:440px;background:rgba(255,255,255,0.07);backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,0.12);border-radius:20px;padding:2.5rem;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,0.4);">

        @if ($signatureRequest->isApproved())
            <div
                style="width:64px;height:64px;background:linear-gradient(135deg,#15803d,#16a34a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;box-shadow:0 0 0 8px rgba(21,128,61,0.12);">
                <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 style="font-family:var(--font-display);color:#fff;font-size:1.4rem;margin-bottom:0.5rem;">Dokumen Disetujui
            </h1>
            <p style="color:rgba(255,255,255,0.55);font-size:0.85rem;line-height:1.55;margin-bottom:1.25rem;">
                Anda telah <strong style="color:#4ade80;">menyetujui</strong> permintaan tanda tangan untuk dokumen
                <strong
                    style="color:rgba(255,255,255,0.8);">{{ $signatureRequest->documentLog->documentType->name }}</strong>.
                <br><br>
                Pemohon (<strong
                    style="color:rgba(255,255,255,0.7);">{{ $signatureRequest->user?->name ?? 'pengguna' }}</strong>)
                akan menerima notifikasi email beserta dokumen terlampir.
            </p>
        @else
            <div
                style="width:64px;height:64px;background:rgba(239,68,68,0.2);border:2px solid rgba(239,68,68,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg style="width:28px;height:28px;color:rgb(239,68,68);" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h1 style="font-family:var(--font-display);color:#fff;font-size:1.4rem;margin-bottom:0.5rem;">Permintaan Ditolak
            </h1>
            <p style="color:rgba(255,255,255,0.55);font-size:0.85rem;line-height:1.55;margin-bottom:1.25rem;">
                Anda telah <strong style="color:#f87171;">menolak</strong> permintaan tanda tangan untuk dokumen
                <strong
                    style="color:rgba(255,255,255,0.8);">{{ $signatureRequest->documentLog->documentType->name }}</strong>.
                <br><br>
                Pemohon akan menerima notifikasi email mengenai penolakan ini.
            </p>
        @endif

        @if ($signatureRequest->notes)
            <div
                style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:0.85rem;margin-bottom:1.25rem;font-size:0.8rem;color:rgba(255,255,255,0.6);text-align:left;">
                <strong
                    style="color:rgba(255,255,255,0.4);font-size:0.65rem;letter-spacing:0.06em;text-transform:uppercase;display:block;margin-bottom:0.3rem;">Catatan
                    Anda</strong>
                {{ $signatureRequest->notes }}
            </div>
        @endif

        <p style="font-size:0.72rem;color:rgba(255,255,255,0.25);line-height:1.5;">
            Tinjauan selesai pada {{ $signatureRequest->reviewed_at->locale('id')->translatedFormat('d F Y, H:i') }}.
            <br>Anda dapat menutup halaman ini.
        </p>
    </div>

</body>

</html>