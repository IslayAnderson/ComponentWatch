<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Site extends Model
{
    protected $fillable = ['user_id', 'name', 'url', 'api_key', 'oauth_client_id', 'oauth_client_secret'];

    protected static function booted(): void
    {
        static::creating(function (Site $site) {
            $site->api_key ??= Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(Component::class);
    }
}
