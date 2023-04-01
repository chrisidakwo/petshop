<?php

declare(strict_types=1);

namespace App\Models;

use App\Auth\Contracts\JwtSubject;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $uuid
 * @property string $first_name
 * @property string $last_name
 * @property int $is_admin
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $avatar UUID of the image stored in files table
 * @property string|null $address
 * @property string|null $phone_number
 * @property int $is_marketing
 * @property string|null $last_login_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $token
 *
 * @property-read Collection<int, JwtToken> $jwtTokens
 * @property-read int|null $jwt_tokens_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAddress($value)
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereIsAdmin($value)
 * @method static Builder|User whereIsMarketing($value)
 * @method static Builder|User whereLastLoginAt($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhoneNumber($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUuid($value)
 *
 * @mixin Eloquent
 */
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $record): void {
            $record->uuid = Str::orderedUuid()->toString();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getAuthIdentifierName(): string
    {
        return 'uuid';
    }

    public function getSubjectIdentifier(): string
    {
        return $this->getAttribute('uuid');
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === 1;
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
