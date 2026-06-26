<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screenshot extends Model
{
    protected $fillable = ['component_id', 'discovery_id', 'path', 'page_url'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function discovery(): BelongsTo
    {
        return $this->belongsTo(Discovery::class);
    }
}
