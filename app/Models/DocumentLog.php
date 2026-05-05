<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\SignatureRequest;

class DocumentLog extends Model
{
    public $timestamps = false;     // why?? because manual

    protected $fillable = [
        'user_id', 'document_type_id', 'output_filename',
        'status', 'generated_at', 'downloaded_at', 'deleted_at',
    ];

    protected function casts() : array 
    {
        return [
            'generated_at' => 'datetime',
            'downloaded_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function user() : BelongsTo { return $this->belongsTo(User::class); }
    public function documentType(): BelongsTo { return $this->belongsTo(DocumentType::class); }
    public function status(): BelongsTo { return $this->belongsTo(DocumentLog::class); }
    public function signatureRequest(): HasOne { return $this->hasOne(SignatureRequest::class); }
}