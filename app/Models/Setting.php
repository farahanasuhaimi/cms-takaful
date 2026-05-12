<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['user_id', 'key', 'value'];

    protected static function booted(): void
    {
        static::addGlobalScope('user', function ($q) {
            if (auth()->check()) {
                $q->where('user_id', auth()->id());
            }
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key, 'user_id' => auth()->id()],
            ['value' => $value]
        );
    }
}
