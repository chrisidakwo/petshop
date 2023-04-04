<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property string $uuid
 * @property string $type
 * @property array $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 *
 * @method static PaymentFactory factory($count = null, $state = [])
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment query()
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereDetails($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment whereType($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @method static Builder|Payment whereUuid($value)
 *
 * @mixin Eloquent
 */
class Payment extends Model
{
    public const TYPE_CREDIT_CARD = 'credit_card';
    public const TYPE_CASH = 'cash_on_delivery';
    public const TYPE_BANK_TRANSFER = 'bank_transfer';

    protected $fillable = ['uuid', 'type', 'details'];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * @return array<string>
     */
    public static function getPaymentTypes(): array
    {
        return [self::TYPE_CREDIT_CARD, self::TYPE_CASH, self::TYPE_BANK_TRANSFER];
    }

    /**
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
