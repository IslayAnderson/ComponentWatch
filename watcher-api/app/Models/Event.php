<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = ['discovery_id', 'type', 'payload', 'occurred_at'];

    protected $casts = ['payload' => 'array', 'occurred_at' => 'datetime'];

    public function discovery(): BelongsTo
    {
        return $this->belongsTo(Discovery::class);
    }
}
