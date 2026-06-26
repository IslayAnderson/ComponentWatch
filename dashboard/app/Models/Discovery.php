<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discovery extends Model
{
    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
