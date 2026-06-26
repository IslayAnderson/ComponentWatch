<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreenshotToken extends Model
{
    protected $fillable = ['component_id', 'token', 'expires_at', 'used_at'];

    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
