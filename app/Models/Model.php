<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function generateUuid(): void
    {
        if (in_array('uuid', $this->fillable)) {
            $this->setAttribute('uuid', Str::orderedUuid()->toString());
        }
    }

    public function updateSlug(): void
    {
        if (in_array('slug', $this->fillable)
            && ($this->wasChanged('title') || $this->getAttribute('title'))) {
            $this->setAttribute('slug', Str::slug($this->getAttribute('title')));
        }
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($record): void {
            $record->generateUuid();
            $record->updateSlug();
        });

        static::updating(function ($record): void {
            $record->updateSlug();
        });
    }
}
