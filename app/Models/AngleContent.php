<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngleContent extends Model
{
    protected $fillable = ['user_id', 'angle_id', 'batch', 'style', 'content', 'is_pinned', 'model'];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

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

    public function angle()
    {
        return $this->belongsTo(ReachAngle::class, 'angle_id');
    }
}
