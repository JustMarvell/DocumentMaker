<?php

namespace App\Notifications;

use App\Models\SignatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SignatureApprovedNotification extends Notification {
    use Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {

    }

    public function via(object $notifiable): array {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage {
        $sr = SignatureRequest::with(['documentLog.documentType', 'official'])
            ->find($this->signatureRequest->id);
        $docType = $sr->documentLog->documentType;
        $official = $sr->official;
        $verifyUrl = route('signature.verify', $sr->token);
        $officialName = $official?->staff_name ?? 'Pejabat';

        $mail = (new MailMessage)
            ->subject("✅ Dokumen Ditandatangani — {$docType->name}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Permintaan tanda tangan digital Anda telah **disetujui** dan dokumen telah ditandatangani secara digital.")
            ->line("**Jenis Dokumen:** {$docType->name}")
            ->line("**Ditandatangani oleh:** {$officialName}")
            ->line("**Tanggal:** " . $sr->reviewed_at->locale('id')->translatedFormat('d F Y, H:i'));

        if ($sr->notes) {
            $mail->line("**Catatan:** {$sr->notes}");
        }

        $mail->line("Dokumen yang telah ditandatangani terlampir dalam email ini.")
            ->line("Keaslian dokumen dapat diverifikasi melalui tautan di bawah:")
            ->action('Verifikasi Keaslian Dokumen', $verifyUrl);

        // Attach signed file (prefer) or fall back to original
        $filePath = $sr->bestFilePath();
        $fileName = $sr->bestFileName();


        Log::info('SignatureApprovedNotification: attaching file', [
            'signed_filename' => $sr->signed_filename,
            'best_file_path' => $filePath,
            'best_file_exists' => $filePath ? file_exists($filePath) : false,
        ]);

        if ($filePath) {
            $mail->attach($filePath, ['as' => $fileName]);
        } else {
            $mail->line("*(File tidak tersedia — mungkin sudah dihapus otomatis. Silakan hubungi administrator.)*");
        }

        $mail->salutation("Salam,\neDokPUPRD — Sistem Pembuatan Dokumen Digital\nDinas PUPRD Kota Tomohon");

        return $mail;
    }
}