<?php

namespace App\Services;

use App\Models\SignatureRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class SignatureService
{
    /**
     * Run sign_document.py on the approved request's document.
     *
     * Returns the signed filename on success, or null on failure.
     * Failures are logged but never thrown — the approval flow must
     * still complete even if signing fails.
     */
    public function sign(SignatureRequest $signatureRequest): ?string
    {
        $docLog = $signatureRequest->documentLog;
        $official = $signatureRequest->official;
        $documentType = $signatureRequest->documentLog->documentType;

        // Build the signed output filename  e.g. "signed_surat-tugas_<uuid>.docx"
        $ext = pathinfo($docLog->output_filename, PATHINFO_EXTENSION);
        $signedFilename = 'signed_' . Str::uuid() . '.' . $ext;

        $pythonBin = base_path('venv/bin/python');
        $scriptPath = base_path('scripts/sign_document.py');

        // Verification URL — public page anyone can visit to confirm authenticity
        $verifyUrl = route('signature.verify', $signatureRequest->token);

        // Approval date in Indonesian format
        $approvalDate = $signatureRequest->reviewed_at
            ? $signatureRequest->reviewed_at->locale('id')->translatedFormat('d F Y')
            : now()->locale('id')->translatedFormat('d F Y');

        $cmd = [
            $pythonBin,
            $scriptPath, 
            '--input', $docLog->output_filename, 
            '--output', $signedFilename, 
            '--sig-image', $official?->signatureImagePath() ?? '', 
            '--verify-url', $verifyUrl, 
            '--official-name', $official?->staff_name ?? '',
            '--official-position', $official?->position ?? '',
            '--approval-date', $approvalDate,
            '--use-image', $documentType->signature_use_image ? '1' : '0',
            '--use-qr', $documentType->signature_use_qr ? '1' : '0',
        ];

        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('SignatureService: signing script failed', [
                'stderr' => $process->getErrorOutput(),
                'stdout' => $process->getOutput(),
                'request' => $signatureRequest->id,
            ]);
            return null;
        }

        Log::info("SignatureService: signed document created — {$signedFilename}");
        return $signedFilename;
    }
}