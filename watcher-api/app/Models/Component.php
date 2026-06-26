<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = ['site_id', 'name', 'description'];

    public function macros(): HasMany
    {
        return $this->hasMany(Macro::class)->orderBy('priority');
    }

    public function discoveries(): HasMany
    {
        return $this->hasMany(Discovery::class);
    }
}
