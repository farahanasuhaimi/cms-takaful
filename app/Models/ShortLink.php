<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
    // Codes map to drtakaful.com/go/{code} — the redirect itself lives in the
    // drtakaful repo's url-map.php. This table is just the copy-ready directory.
    public const BASE_URL = 'https://drtakaful.com/go/';

    protected $fillable = ['user_id', 'section', 'code', 'title', 'target', 'notes'];

    protected static function booted(): void
    {
        static::addGlobalScope('user', function ($q) {
            if (auth()->check()) {
                $q->where('user_id', auth()->id());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return self::BASE_URL . $this->code;
    }
}
