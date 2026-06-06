<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngleUsage extends Model
{
    protected $fillable = ['user_id', 'angle_id', 'lead_id', 'client_id', 'used_on', 'notes'];

    protected $casts = ['used_on' => 'date'];

    protected static function booted(): void
    {
        static::addGlobalScope('user', function ($q) {
            if (auth()->check()) {
                $q->where('user_id', auth()->id());
            }
        });
    }

    public function angle()
    {
        return $this->belongsTo(ReachAngle::class, 'angle_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
