<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyPost extends Model
{
    protected $fillable = ['user_id', 'reach_angle_id', 'post_date', 'platform', 'topic', 'caption', 'image_prompt', 'status'];

    protected $casts = [
        'post_date'    => 'date',
        'image_prompt' => 'array',
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

    public function reachAngle()
    {
        return $this->belongsTo(ReachAngle::class);
    }

    public function isPosted(): bool
    {
        return $this->status === 'posted';
    }

    public function hasContent(): bool
    {
        return ! empty($this->caption);
    }
}
