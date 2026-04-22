<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficialData extends Model
{
    protected $table = 'official_data';

    protected $fillable = [
        'staff_name',
        'nip',
        'email',
        'phone_number',
        'rank',
        'position',
        'work_unit',
    ];
}
