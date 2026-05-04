<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sudah Ditinjau — eDokPUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(160deg, #0a0f1e 0%, #0d1526 60%, #101c38 100%);
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
        style="width:100%;max-width:420px;background:rgba(255,255,255,0.07);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.1);border-radius:18px;padding:2.5rem;text-align:center;">

        <div
            style="width:56px;height:56px;background:rgba(201,168,76,0.15);border:2px solid rgba(201,168,76,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
            <svg style="width:24px;height:24px;color:var(--gold-400);" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 style="font-family:var(--font-display);color:#fff;font-size:1.3rem;margin-bottom:0.5rem;">Sudah Ditinjau
        </h1>
        <p style="color:rgba(255,255,255,0.5);font-size:0.83rem;line-height:1.55;margin-bottom:1.25rem;">
            Permintaan tanda tangan ini sudah pernah ditinjau sebelumnya dan tidak dapat diproses ulang.
        </p>

        <div
            style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:0.9rem;font-size:0.8rem;text-align:left;">
            <div
                style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                <span style="color:rgba(255,255,255,0.4);">Dokumen</span>
                <span
                    style="color:rgba(255,255,255,0.8);font-weight:500;">{{ $signatureRequest->documentLog->documentType->name }}</span>
            </div>
            <div
                style="display:flex;justify-content:space-between;padding:0.3rem 0;border-bottom:1px solid rgba(255,255,255,0.06);">
                <span style="color:rgba(255,255,255,0.4);">Status</span>
                <span style="font-weight:600;color:{{ $signatureRequest->isApproved() ? '#4ade80' : '#f87171' }};">
                    {{ $signatureRequest->isApproved() ? 'Disetujui' : 'Ditolak' }}
                </span>
            </div>
            @if ($signatureRequest->reviewed_at)
                <div style="display:flex;justify-content:space-between;padding:0.3rem 0;">
                    <span style="color:rgba(255,255,255,0.4);">Ditinjau pada</span>
                    <span
                        style="color:rgba(255,255,255,0.7);">{{ $signatureRequest->reviewed_at->locale('id')->translatedFormat('d F Y') }}</span>
                </div>
            @endif
        </div>

        <p style="font-size:0.7rem;color:rgba(255,255,255,0.2);margin-top:1.25rem;">Anda dapat menutup halaman ini.</p>
    </div>
</body>

</html>