<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

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
