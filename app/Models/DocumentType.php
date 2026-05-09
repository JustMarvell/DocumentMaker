<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DocumentType extends Model
{
    protected $fillable = [
        'name', 'key', 'script_name', 'template_filename', 
        'output_filename', 'access_level', 'is_active',
        'file_type', 'preview_enabled', 'signature_enabled',
        'signature_use_image', 'signature_use_qr',
        'preview_pdf',
    ];

    protected function casts() : array {
        return [
            'is_active' => 'boolean',
            'preview_enabled' => 'boolean',
            'signature_enabled' => 'boolean',
            'signature_use_image' => 'boolean',
            'signature_use_qr' => 'boolean',
        ];
    }

    // relationship type shii
    public function documentLogs() : HasMany {
        return $this->hasMany(DocumentLog::class);
    }

    public function fields() : HasMany {
        return $this->hasMany(DocumentField::class)->orderBy('sort_order');
    }

    public function topLevelFields() : HasMany {
        return $this->hasMany(DocumentField::class)
            ->where('is_group_child', false)
            ->orderBy('sort_order');
    }

    // scopessldkfjlsdfjs
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeAccessibleBy($query, string $role) {
        if (in_array($role, ['staff', 'admin'])) {
            return $query;
        }
        return $query->where('access_level', 'guest');
    }

    public function slots() : HasMany {
        return $this->hasMany(DocumentAutofillSlot::class)->orderBy('sort_order');  
    }

    public function numberCounter(): HasOne {
        return $this->hasOne(DocumentNumberCounter::class);
    }
}