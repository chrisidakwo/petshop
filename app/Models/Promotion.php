<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Database\Factories\PromotionFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Promotion
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $content
 * @property array $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static PromotionFactory factory($count = null, $state = [])
 * @method static Builder|Promotion newModelQuery()
 * @method static Builder|Promotion newQuery()
 * @method static Builder|Promotion query()
 * @method static Builder|Promotion whereContent($value)
 * @method static Builder|Promotion whereCreatedAt($value)
 * @method static Builder|Promotion whereId($value)
 * @method static Builder|Promotion whereMetadata($value)
 * @method static Builder|Promotion whereTitle($value)
 * @method static Builder|Promotion whereUpdatedAt($value)
 * @method static Builder|Promotion whereUuid($value)
 *
 * @mixin Eloquent
 */
class Promotion extends Model
{
    protected $fillable = ['uuid', 'title', 'content', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
