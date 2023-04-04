<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\JwtToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $unique_id
 * @property string $token_title
 * @property array|null $restrictions
 * @property array|null $permissions
 * @property Carbon|null $expires_at
 * @property Carbon|null $last_used_at
 * @property Carbon|null $refreshed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read User $user
 *
 * @method static Builder|JwtToken newModelQuery()
 * @method static Builder|JwtToken newQuery()
 * @method static Builder|JwtToken query()
 * @method static Builder|JwtToken whereCreatedAt($value)
 * @method static Builder|JwtToken whereExpiresAt($value)
 * @method static Builder|JwtToken whereId($value)
 * @method static Builder|JwtToken whereLastUsedAt($value)
 * @method static Builder|JwtToken wherePermissions($value)
 * @method static Builder|JwtToken whereRefreshedAt($value)
 * @method static Builder|JwtToken whereRestrictions($value)
 * @method static Builder|JwtToken whereTokenTitle($value)
 * @method static Builder|JwtToken whereUniqueId($value)
 * @method static Builder|JwtToken whereUpdatedAt($value)
 * @method static Builder|JwtToken whereUserId($value)
 *
 * @mixin Eloquent
 */
class JwtToken extends Model
{
    protected $fillable = [
        'unique_id', 'user_id', 'token_title', 'restrictions', 'permissions', 'expires_at', 'last_used_at',
    ];

    protected $casts = [
        'restrictions' => 'array',
        'permissions' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'refreshed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, JwtToken>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
