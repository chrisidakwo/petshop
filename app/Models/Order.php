<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $user_id
 * @property string $order_status_uuid
 * @property string|null $payment_uuid
 * @property string $uuid
 * @property array<string, mixed> $products
 * @property array $address
 * @property float|null $delivery_fee
 * @property float $amount
 * @property Carbon|null $shipped_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read OrderStatus $orderStatus
 * @property-read Payment|null $payment
 * @property-read User $user
 *
 * @method static OrderFactory factory($count = null, $state = [])
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddress($value)
 * @method static Builder|Order whereAmount($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDeliveryFee($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereOrderStatusUuid($value)
 * @method static Builder|Order wherePaymentUuid($value)
 * @method static Builder|Order whereProducts($value)
 * @method static Builder|Order whereShippedAt($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUserId($value)
 * @method static Builder|Order whereUuid($value)
 *
 * @mixin Eloquent
 */
class Order extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'order_status_uuid', 'products', 'address', 'delivery_fee', 'amount', 'shipped_at',
        'payment_uuid',
    ];

    protected $casts = [
        'products' => 'array',
        'address' => 'array',
        'shipped_at' => 'datetime',
    ];

    /**
     * @var array<string> $with
     */
    protected $with = ['orderStatus', 'payment', 'user'];

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
                'quantity' => $quantity,
            ];

            $returnProducts[] = $productArr;

            return $returnProducts;
        }, []);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getOriginalProducts(): array
    {
        $rawValue = json_decode($this->getRawOriginal('products'));

        $products = [];

        foreach ($rawValue as $value) {
            $products[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
            ];
        }

        return $products;
    }

    public function subTotalAmount(): float|int
    {
        return array_reduce($this->products, function ($sum, $product): float {
            return $sum + ($product['quantity'] * $product['price']);
        }, 0);
    }

    /**
     * @return BelongsTo<OrderStatus, Order>
     */
    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_uuid', 'uuid');
    }

    /**
     * @return BelongsTo<Payment, Order>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_uuid', 'uuid');
    }

    /**
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
