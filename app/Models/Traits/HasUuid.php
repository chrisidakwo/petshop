<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($record) {
            if (! $record->uuid) {
                $record->uuid = Str::orderedUuid()->toString();
            }
        });
    }
}
