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

    /**
     * Next actions that are past due and still open. A next action only stays open
     * while it belongs to the person's latest touchpoint — logging anything newer
     * for the same client/lead supersedes it.
     */
    public function scopeOverdueFollowUps($query)
    {
        return $query->whereNotNull('next_action')
            ->whereNotNull('next_action_date')
            ->where('next_action_date', '<', now()->startOfDay())
            ->whereNotExists(function ($newer) {
                $newer->from('touchpoints as newer')
                    ->whereColumn('newer.touchable_type', 'touchpoints.touchable_type')
                    ->whereColumn('newer.touchable_id', 'touchpoints.touchable_id')
                    ->where(function ($q) {
                        $q->whereColumn('newer.contacted_at', '>', 'touchpoints.contacted_at')
                          ->orWhere(function ($q) {
                              $q->whereColumn('newer.contacted_at', 'touchpoints.contacted_at')
                                ->whereColumn('newer.id', '>', 'touchpoints.id');
                          });
                    });
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
