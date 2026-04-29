<?php

namespace App\Notifications;

use App\Models\SignatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignatureRejectedNotification extends Notification {
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
        $official = $sr->official;

        $officialName = $official?->staff_name ?? 'Pejabat';

        $mail = (new MailMessage)
            ->subject("Permintaan Tanda Tangan Ditolak - {$docType->name}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Mohon maaf, permintaan tanda tangan digital anda **Tidak Disetujui**.")
            ->line("**Jenis Dokumen:** {$docType->name}")
            ->line("**Ditinjau oleh:** {$officialName}")
            ->line("**Tanggal Tinjauan:** " . $sr->reviewed_at->locale('id')->translatedFormat('d F Y, H:i'));

        if ($sr->notes) {
            $mail->line("**Alasan / Catatan**: {$sr->notes}");
        }

        $mail->line("Anda dapat membuat ulang dokumen dan mengajukan permintaan baru setelah melakukan perbaikan.")
            ->line("Jika ada pertanyaan, silakan hubungi administrator.")
            ->salutation("Salam,\nSIPADU — Sistem Generasi Administrasi Persuratan\nDinas PUPRD Kota Tomohon");

        return $mail;
    }
}