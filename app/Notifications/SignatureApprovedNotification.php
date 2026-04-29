<?

namespace App\Notifications;

use App\Models\SignatureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignatureApprovedNotification extends Notification {
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
        $filePath = $sr->documentFilePath();
        $fileName = $docLog->output_filename;

        $officialName = $official?->staff_name ?? 'Pejabat';

        $mail = (new MailMessage)
            ->subject("Dokumen Disetujui! - {$docType->name}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Permintaan tanda tangan digital Anda telah **Disetujui**.")
            ->line("**Jenis Dokumen:** {$docType->name}")
            ->line("**Disetujui oleh:** {$officialName}")
            ->line("**Tanggal Persetujuan:** " . $sr->reviewed_at->locale('id')->translatedFormat('d F Y, H:i'));

        if ($sr->notes) {
            $mail->line("**Catatan:** {$sr->notes}");
        }

        $mail->line("Dokumen yang telah disetujui terlampir dalam email ini.");

        if ($sr->documentFileExists()) {
            $mail->attach($filePath, ['as' => $fileName]);
        } else {
            $mail->line("*(File dokumen tidak tersedia - Mungkin sudah dihapus otomatis dari server. Silahkan buat ulang dokumen jika diperlukan.)*");
        }

        $mail->line("Terimakasih telah menggunaakan *SIPADU*.")
            ->salutation("Salam,\nSIPADU - Sistem Generasi Administrasi Persuratan\nDINAS PUPRD Kota Tomohon");
        
        return $mail;
    }
}