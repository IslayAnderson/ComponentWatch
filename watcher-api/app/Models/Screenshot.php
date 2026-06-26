<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screenshot extends Model
{
    protected $fillable = ['component_id', 'path', 'page_url'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
