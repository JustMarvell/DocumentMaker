<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentField extends Model
{
    protected $fillable = [
        'document_type_id',
        'field_key',
        'label',
        'field_type',
        'field_options',
        'is_required',
        'sort_order',
        'row_group',
        'section_label',
        'group_key',
        'is_group_child',
        'staff_autofill_column',
        'autofill_role',
    ];

    protected function casts() : array {
        return [
            'field_options' => 'array',
            'is_required' => 'boolean',
            'is_group_child' => 'boolean',
        ];
    }

    // relation type shii
    public function documentType() : BelongsTo {
        return $this->belongsTo(DocumentType::class);
    }

    // child field repeating group typa shii
    public function children(): HasMany {
        return $this->hasMany(DocumentField::class, 'group_key', 'field_key')
            ->where('document_type_id', $this->document_type_id)
            ->where('is_group_child', true)
            ->orderBy('sort_order');
    }

    public static function fieldTypes(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'date' => 'Date',
            'number' => 'Number',
            'select' => 'Select (Dropdown)',
            'checkbox' => 'Checkbox',
            'repeating_group' => 'Repeating Group (Loop)',
            'staff_loop' => 'Staff Loop (pilih dari data staff)',
            'official_loop' => 'Official Loop (pilih dari data pejabat)',
        ];
    }

    public static function staffColumns() : array {
        return [
            'staff_name' => 'Nama Staff',
            'nip' => 'NIP',
            'email' => 'Email',
            'phone_number' => 'No. HP',
            'rank' => 'Jabatan / Gol. Pangkat',
            'position' => 'Posisi',
            'work_unit' => 'Unit Kerja',
        ];
    }
}
