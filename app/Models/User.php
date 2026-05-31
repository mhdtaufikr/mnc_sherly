<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'role',
        'status',
        'is_active',
        'password_changed_at',
        'last_login',
        'login_counter',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password_changed_at' => 'datetime',
            'last_login'          => 'datetime',
            'is_active'           => 'boolean',
            'password'            => 'hashed',
        ];
    }
}
