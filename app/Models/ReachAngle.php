<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReachAngle extends Model
{
    protected $fillable = ['title', 'description', 'target_segment', 'status'];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'angle_client')->withPivot('reached_at');
    }
}
