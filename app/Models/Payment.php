<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasUuid;

    public const TYPE_CREDIT_CARD = 'credit_card';
    public const TYPE_CASH = 'cash';
    public const TYPE_BANK_TRANSFER = 'bank_transfer';

    protected $fillable = ['uuid', 'type', 'details'];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * @return string[]
     */
    public static function getPaymentTypes(): array
    {
        return [self::TYPE_CREDIT_CARD, self::TYPE_CASH, self::TYPE_BANK_TRANSFER];
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
