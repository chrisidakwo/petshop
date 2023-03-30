<?php

declare(strict_types=1);

namespace App\Models;

use App\Auth\Contracts\JwtSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, JwtSubject
{
    use HasApiTokens, HasFactory, Notifiable, \Illuminate\Auth\MustVerifyEmail;

    /**
     * @var array<string>
     */
    protected $fillable = [
        'uuid', 'first_name', 'last_name', 'is_admin', 'email', 'password', 'avatar', 'address', 'phone_number',
        'is_marketing', 'last_login_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getSubjectIdentifier(): string
    {
        return $this->getAttribute('uuid');
    }

    /**
     * @return HasMany<JwtToken>
     */
    public function jwtTokens(): HasMany
    {
        return $this->hasMany(JwtToken::class);
    }

    /**
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
