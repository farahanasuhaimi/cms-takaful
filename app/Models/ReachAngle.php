<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReachAngle extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'target_segment', 'notes', 'status'];

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

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'angle_client')->withPivot('reached_at');
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'angle_lead')->withPivot('linked_at');
    }

    public function strategies()
    {
        return $this->belongsToMany(Strategy::class, 'angle_strategy')->withPivot('linked_at');
    }

    public function usages()
    {
        return $this->hasMany(AngleUsage::class, 'angle_id')->latest('used_on');
    }

    public function contents()
    {
        return $this->hasMany(AngleContent::class, 'angle_id');
    }

    public function latestContents()
    {
        return $this->hasMany(AngleContent::class, 'angle_id')
            ->whereIn('batch', function ($q) {
                $q->selectRaw('MAX(ac2.batch)')
                  ->from('angle_contents as ac2')
                  ->whereColumn('ac2.angle_id', 'angle_contents.angle_id');
            });
    }
}
