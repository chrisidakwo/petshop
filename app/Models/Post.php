<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PostFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property array $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static PostFactory factory($count = null, $state = [])
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereContent($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereMetadata($value)
 * @method static Builder|Post whereSlug($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereUuid($value)
 *
 * @mixin Eloquent
 */
class Post extends Model
{
    protected $fillable = ['uuid', 'title', 'slug'];

    protected $casts = [
        'metadata' => 'array',
    ];
}
