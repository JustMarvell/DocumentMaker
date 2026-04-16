<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffData extends Model
{
    protected $table = 'staff_data';

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
