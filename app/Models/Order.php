<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'user_id', 'order_status_id', 'products', 'address', 'delivery_fee', 'amount', 'shipped_at',
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
        'shipped_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
