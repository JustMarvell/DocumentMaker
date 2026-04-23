<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAutofillSlot extends Model
{
    protected $fillable = [
        'document_type_id', 
        'slot_key',
        'slot_label',
        'sort_order',
    ];

    public function documentType() : BelongsTo {
        return $this->belongsTo(DocumentType::class);
    }
}
