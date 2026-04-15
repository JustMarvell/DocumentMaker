<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'nip', 'email','password', 'role', 'work_unit',
    ];

    protected $hidden = [
        'password', 'remember_token', 
    ];

    protected function casts() : array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // relationshipshdsfsdkfsldfj
    public function documentLogs() : HasMany
    {
        return $this->hasMany(DocumentLog::class);
    }

    // role helper type shii
    public function isAdmin() : bool { return $this->role === 'admin'; }
    public function isStaff() : bool { return $this->role === 'staff'; }
    public function isGuest() : bool { return $this->role === 'guest'; }

    public function hasAccesTo(DocumentType $type) : bool 
    {
        if ($this->isAdmin()) return true;
        if ($this->isStaff()) return true;
        return $type->access_level === 'guest';
    }
}
