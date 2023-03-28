<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Str;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
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

    public function generateUuid(): void
    {
        if (in_array('uuid', $this->fillable)) {
            $this->uuid = Str::orderedUuid()->toString();
        }
    }

    public function updateSlug(): void
    {
        if ($this->slug && $this->wasChanged('title')) {
            $this->slug = Str::slug($this->title);
        }
    }
}
