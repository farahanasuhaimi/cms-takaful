<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'phone', 'ic_no', 'email', 'notes'];

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
