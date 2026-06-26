<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = ['user_id', 'name', 'url', 'api_key'];

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
