<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = [
        'name', 'key', 'script_name', 'template_filename', 
        'output_filename', 'access_level', 'is_active',
        'file_type',
    ];

    protected function casts() : array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // relationship type shii
    public function documentLogs() : HasMany
    {
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAccessibleBy($query, string $role)
    {
        if (in_array($role, ['staff', 'admin']))
        {
            return $query;
        }
        return $query->where('access_level', 'guest');
    }

    public function slots() : HasMany {
        return $this->hasMany(DocumentAutofillSlot::class)->orderBy('sort_order');  
    }
}