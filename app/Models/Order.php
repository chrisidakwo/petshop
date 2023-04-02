<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrderFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_status_id
 * @property int|null $payment_id
 * @property string $uuid
 * @property array $products
 * @property array $address
 * @property float|null $delivery_fee
 * @property float $amount
 * @property Carbon|null $shipped_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OrderStatus $orderStatus
 * @property-read Payment|null $payment
 * @property-read User $user
 * @method static OrderFactory factory($count = null, $state = [])
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddress($value)
 * @method static Builder|Order whereAmount($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDeliveryFee($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereOrderStatusId($value)
 * @method static Builder|Order wherePaymentId($value)
 * @method static Builder|Order whereProducts($value)
 * @method static Builder|Order whereShippedAt($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUserId($value)
 * @method static Builder|Order whereUuid($value)
 * @mixin Eloquent
 */
class Order extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'order_status_id', 'products', 'address', 'delivery_fee', 'amount', 'shipped_at',
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
        'shipped_at' => 'datetime',
    ];

    /**
     * @return array<string, mixed>
     */
    public function getProductsAttribute(mixed $value): array
    {
        $value = json_decode($value);

        $productsUuids = Arr::pluck($value, 'product_id');

        $products = Product::query()->whereIn('uuid', $productsUuids)->get();

        return array_reduce($value, function ($returnProducts, $currentValue) use ($products) {
            $uuid = $currentValue->product_id;
            $quantity = $currentValue->quantity;

            /** @var Product $product */
            $product = $products->where('uuid', $uuid)->first();

            $productArr = [
                'uuid' => $product->uuid,
                'price' => round($product->price, 2),
                'product' => $product->title,
                'quantity' => $quantity
            ];

            $returnProducts[] = $productArr;

            return $returnProducts;
        }, []);
    }

    /**
     * @return BelongsTo<OrderStatus, Order>
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class);
    }

    /**
     * @return BelongsTo<Payment, Order>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
