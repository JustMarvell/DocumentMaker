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
}
