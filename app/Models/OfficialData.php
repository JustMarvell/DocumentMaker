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
    ];

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
