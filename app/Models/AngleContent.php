<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngleContent extends Model
{
    protected $fillable = ['angle_id', 'batch', 'style', 'content', 'is_pinned', 'model'];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    public function angle()
    {
        return $this->belongsTo(ReachAngle::class, 'angle_id');
    }
}
