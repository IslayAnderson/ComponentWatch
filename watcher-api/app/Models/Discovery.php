<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discovery extends Model
{
    protected $fillable = ['component_id', 'page_url', 'html_hash', 'stack_position', 'session_id'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
