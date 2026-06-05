<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Touchpoint extends Model
{
    protected $fillable = [
        'user_id', 'strategy_id', 'touchable_type', 'touchable_id',
        'contacted_at', 'channel', 'topic', 'notes',
        'next_action', 'next_action_date',
    ];

    protected $casts = [
        'contacted_at'     => 'datetime',
        'next_action_date' => 'date',
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

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    public function touchable()
    {
        return $this->morphTo();
    }
}
