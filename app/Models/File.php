<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\FileFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $path
 * @property int $size
 * @property string $type File mime type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static FileFactory factory($count = null, $state = [])
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereName($value)
 * @method static Builder|File wherePath($value)
 * @method static Builder|File whereSize($value)
 * @method static Builder|File whereType($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUuid($value)
 *
 * @mixin Eloquent
 */
class File extends Model
{
    protected $fillable = ['uuid', 'name', 'path', 'size', 'type'];
}
