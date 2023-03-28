<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($record) {
            if (! $record->slug) {
                $record->slug = Str::slug($record->title);
            }
        });

        static::updating(function ($record) {
            if (in_array('title', $record->getDirty())) {
                $record->slug = Str::slug($record->title);
            }
        });
    }
}
