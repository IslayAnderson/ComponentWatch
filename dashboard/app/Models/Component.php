<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Component extends Model
{
    protected $fillable = ['site_id', 'name', 'description'];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function macros(): HasMany
    {
        return $this->hasMany(Macro::class)->orderBy('priority');
    }

    public function discoveries(): HasMany
    {
        return $this->hasMany(Discovery::class);
    }

    public function events(): HasManyThrough
    {
        return $this->hasManyThrough(Event::class, Discovery::class);
    }
}
