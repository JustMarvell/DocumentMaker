<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class OfficialData extends Model
{
    use Notifiable;

    protected $table = 'official_data';

    protected $fillable = [
        'staff_name',
        'nip',
        'email',
        'phone_number',
        'rank',
        'position',
        'work_unit',
        'signature_image',
        'can_sign',
    ];

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
    
    /** Absolute path to the signature image, or null if not set / missing */
    public function signatureImagePath(): ?string
    {
        if (!$this->signature_image) {
            return null;
        }
        $path = storage_path('app/signatures/' . $this->signature_image);
        return file_exists($path) ? $path : null;
    }

    public function hasSignatureImage(): bool
    {
        return $this->signatureImagePath() !== null;
    }

}
