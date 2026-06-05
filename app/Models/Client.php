<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['user_id', 'lead_id', 'name', 'phone', 'ic_no', 'email', 'notes'];

    protected $casts = [
        'name'  => 'encrypted',
        'phone' => 'encrypted',
        'ic_no' => 'encrypted',
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

    public function lead()
    {
        return $this->belongsTo(Lead::class)->withoutGlobalScopes();
    }

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function touchpoints()
    {
        return $this->morphMany(Touchpoint::class, 'touchable')->latest('contacted_at');
    }

    public function reachAngles()
    {
        return $this->belongsToMany(ReachAngle::class, 'angle_client')->withPivot('reached_at');
    }

    public function lastTouchpoint()
    {
        return $this->touchpoints()->latest('contacted_at')->first();
    }
}
