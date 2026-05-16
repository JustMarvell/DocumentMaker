<?php

namespace App\Services;

use App\Models\PdfConversionSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Converts docx/xlsx → PDF using the iLoveAPI REST API.
 * Docs: https://developer.ilovepdf.com/docs/api-reference
 *
 * Flow:
 *  1. POST /v1/auth  → bearer token
 *  2. POST /v1/start/officetopdf  → server + task id
 *  3. POST /v1/upload  → upload file
 *  4. POST /v1/process  → trigger conversion
 *  5. GET  /v1/download/{task}  → download PDF bytes
 */
class IlovePdfConverter
{
    public function convert(string $sourceFilename): ?string
    {
        $setting = PdfConversionSetting::instance();

        if (!$setting->hasQuota()) {
            Log::warning('IlovePdfConverter: monthly quota exhausted.');
            return null;
        }

        $publicKey = $setting->iloveapi_public_key ?? config('services.iloveapi.public_key');
        $secretKey = $setting->iloveapi_secret_key ?? config('services.iloveapi.secret_key');

        if (!$publicKey || !$secretKey) {
            Log::error('IlovePdfConverter: API keys not configured.');
            return null;
        }

        $sourceDir = storage_path('app/cached_result');
        $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $sourceFilename;

        if (!file_exists($sourcePath)) {
            Log::error("IlovePdfConverter: source not found: {$sourcePath}");
            return null;
        }

        try {
            // 1. Auth
            $authRes = Http::timeout(30)->connectTimeout(10)->post(config('services.iloveapi.base_url') . '/auth', [
                'public_key' => $publicKey,
            ]);
            if (!$authRes->successful()) {
                Log::error('IlovePdfConverter: auth failed', ['body' => $authRes->body()]);
                return null;
            }
            $token = $authRes->json('token');

            Log::info("ILovePdfConverter: Step 1, Auth Success | token : {$token}");

            // 2. Start task
            $startRes = Http::timeout(90)
                ->connectTimeout(15)
                ->withToken($token)
                ->get(config('services.iloveapi.base_url') . '/start/officepdf');
            if (!$startRes->successful()) {
                Log::error('IlovePdfConverter: start failed', ['body' => $startRes->body()]);
                return null;
            }
            $server = $startRes->json('server');
            $taskId = $startRes->json('task');

            Log::info("ILovePdfConverter: Step 2, Start Task Success | server : {$server}\nTask ID : {$taskId}");

            // 3. Upload
            $uploadRes = Http::timeout(120)
                ->connectTimeout(15)
                ->withToken($token)
                ->attach('file', fopen($sourcePath, 'r'), $sourceFilename)
                ->post("https://{$server}/v1/upload", ['task' => $taskId]);
            if (!$uploadRes->successful()) {
                Log::error('IlovePdfConverter: upload failed', ['body' => $uploadRes->body()]);
                return null;
            }
            $serverFilename = $uploadRes->json('server_filename');

            Log::info("ILovePdfConverter: Step 3, Upload Success | Server Filename : {$serverFilename}");

            // 4. Process
            $processRes = Http::timeout(120)
                ->connectTimeout(15)
                ->withToken($token)
                ->post("https://{$server}/v1/process", [
                    'task' => $taskId,
                    'tool' => 'officepdf',
                    'files' => [
                        [
                            'server_filename' => $serverFilename,
                            'filename' => $sourceFilename,
                        ]
                    ],
                ]);
            if (!$processRes->successful()) {
                Log::error('IlovePdfConverter: process failed', ['body' => $processRes->body()]);
                return null;
            }

            Log::info("ILovePdfConverter: Step 4, Process Success Success");

            // 5. Download
            $downloadRes = Http::timeout(120)
                ->connectTimeout(15)
                ->withToken($token)
                ->get("https://{$server}/v1/download/{$taskId}");
            if (!$downloadRes->successful()) {
                Log::error('IlovePdfConverter: download failed');
                return null;
            }

            
            $pdfFilename = pathinfo($sourceFilename, PATHINFO_FILENAME) . '.pdf';
            $pdfPath = $sourceDir . DIRECTORY_SEPARATOR . $pdfFilename;
            file_put_contents($pdfPath, $downloadRes->body());

            Log::info("ILovePdfConverter: Step 5, Download Success | PDF Filename : {$pdfFilename}\nPDF Path : {$pdfPath}");

            $setting->incrementUsage();

            return $pdfFilename;

        } catch (\Throwable $e) {
            Log::error('IlovePdfConverter: exception — ' . $e->getMessage());
            return null;
        }
    }
}