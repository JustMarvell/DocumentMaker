<?php

namespace App\Notifications;

use App\Models\SignatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignatureRequestedNotification extends Notification {
    use Queueable;

    public function __construct(public SignatureRequest $signatureRequest) {

    }

    public function via(object $notifiable): array {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage {
        $sr = $this->signatureRequest;
        $docLog = $sr->documentLog;
        $docType = $docLog->documentType;
        $requester = $sr->user;
        $reviewUrl = route('signature.review', $sr->token);
        $filePath = $sr->documentFilePath();
        $filename = $docLog->output_filename;

        $requesterName = $requester?->name ?? 'Tanpa Nama';

        $mail = (new MailMessage)
            ->subject("Permintaan Tanda Tangan Digital - {$docType->name}")
            ->greeting("Yth. {$notifiable->staff_name},")
            ->line("Anda menerima permintaan tanda tangan digital untuk dokumen berikut: ")
            ->line("**Jenis Dokumen :**  {$docType->name}")
            ->line("**Diminta Oleh:** {$requesterName}")
            ->line("*Tanggal Permintaan:** " . $sr->requested_at->locale('id')->translatedFormat('d F Y, H:i'))
            ->line("Silahkan buka tautan di bawah ini untuk meninjau dan menandatangani dokumen:")
            ->action('Tinjau & Tanda Tangani Dokumen', $reviewUrl)
            ->line("Tautan di atas hanya dapat digunakan satu kali dan khusus untuk permintaan ini.")
            ->line("Jika anda merasa tidak seharusnya menerima email ini, abaikan saja.")
            ->salutation("Terima Kasih, \n**SIPADU** - Sistem Generasi Administrasi Persuratan\nDinas PUPRD Kota Tomohon");

        if ($sr->documentFileExists()) {
            $mail->attach($filePath, ['as' => $filename]);
        }

        return $mail;
    }
}