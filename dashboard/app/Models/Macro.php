<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Macro extends Model
{
    protected $fillable = ['component_id', 'type', 'value', 'priority'];

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }
}
