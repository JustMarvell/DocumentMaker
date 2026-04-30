<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureRequest extends Model
{
    protected $fillable = [
        'user_id',
        'document_log_id',
        'official_id',
        'status',
        'token',
        'notes',
        'requested_at',
        'reviewed_at',
    ];

    protected function casts(): array {
        return [
            'requested_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function documentLog(): BelongsTo {
        return $this->belongsTo(DocumentLog::class);
    }

    public function official(): BelongsTo {
        return $this->belongsTo(OfficialData::class, 'official_id');
    }

    public function isPending(): bool {
        return $this->status === 'pending';
    }

    public function isApproved(): bool {
        return $this->status === 'approved';
    }

    public function isRejected(): bool {
        return $this->status === 'rejected';
    }

    public static function generateToken(): string {
        return bin2hex(random_bytes(32));
    }

    public function documentFilePath(): string {
        return public_path('cached_result/' . $this->documentLog->output_filename);
    }

    public function documentFileExists(): bool {
        return file_exists($this->documentFilePath());
    }

    public function signedFilePath(): ?string
    {
        if (!$this->signed_filename)
            return null;
        $path = public_path('cached_result/' . $this->signed_filename);
        return file_exists($path) ? $path : null;
    }

    /** Prefer signed file, fall back to original */
    public function bestFilePath(): ?string
    {
        return $this->signedFilePath()
            ?? ($this->documentFileExists() ? $this->documentFilePath() : null);
    }

    public function bestFileName(): string
    {
        return $this->signed_filename ?? $this->documentLog->output_filename;
    }
}
