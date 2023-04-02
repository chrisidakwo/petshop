<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $category_uuid
 * @property string $uuid
 * @property string $title
 * @property float $price
 * @property string $description
 * @property array $metadata
 * @property string|null $brand_uuid
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Brand|null $brand
 * @property-read Category $category
 * @method static ProductFactory factory($count = null, $state = [])
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product whereBrandUuid($value)
 * @method static Builder|Product whereCategoryUuid($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereMetadata($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product whereUuid($value)
 * @method static Builder|Product withTrashed()
 * @method static Builder|Product withoutTrashed()
 * @mixin Eloquent
 */
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_uuid', 'title', 'uuid', 'price', 'description', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_uuid', 'uuid');
    }

    /**
     * @return BelongsTo<Category, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }
}
