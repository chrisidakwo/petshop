<?php

namespace App\Models;

 use App\Models\Traits\HasUuid;
 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, \Illuminate\Auth\MustVerifyEmail, HasUuid;

    protected $fillable = [
        'uuid', 'first_name', 'last_name', 'is_admin', 'email', 'password', 'avatar', 'address', 'phone_number',
        'is_marketing', 'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
