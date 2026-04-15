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

    public function contents()
    {
        return $this->hasMany(AngleContent::class, 'angle_id');
    }

    public function pinnedContents()
    {
        return $this->hasMany(AngleContent::class, 'angle_id')->where('is_pinned', true)->latest();
    }
}
