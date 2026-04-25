<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class PdfConverter
{
    /**
     * Convert a file in public/cached_result/ to PDF using LibreOffice.
     * Returns the PDF filename (same base name, .pdf extension) on success,
     * or null on failure.
     */
    public function convert(string $sourceFilename): ?string
    {
        $sourceDir = public_path('cached_result');
        $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $sourceFilename;

        if (!file_exists($sourcePath)) {
            Log::error("PdfConverter: source file not found: {$sourcePath}");
            return null;
        }

        // LibreOffice outputs the PDF in the same directory as the source file
        $cmd = [
            'libreoffice',
            '--headless',
            '--convert-to',
            'pdf',
            '--outdir',
            $sourceDir,
            $sourcePath,
        ];

        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error("PdfConverter: LibreOffice failed for {$sourceFilename}: " . $process->getErrorOutput());
            return null;
        }

        // LibreOffice replaces the extension with .pdf
        $pdfFilename = pathinfo($sourceFilename, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = $sourceDir . DIRECTORY_SEPARATOR . $pdfFilename;

        if (!file_exists($pdfPath)) {
            Log::error("PdfConverter: PDF not found after conversion: {$pdfPath}");
            return null;
        }

        return $pdfFilename;
    }

    /**
     * Check if LibreOffice is available on this system.
     */
    public static function isAvailable(): bool
    {
        $process = new Process(['libreoffice', '--version']);
        $process->setTimeout(10);
        $process->run();
        return $process->isSuccessful();
    }
}