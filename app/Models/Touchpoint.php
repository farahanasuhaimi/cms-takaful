<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Touchpoint extends Model
{
    protected $fillable = [
        'touchable_type', 'touchable_id',
        'contacted_at', 'channel', 'topic', 'notes',
        'next_action', 'next_action_date',
    ];

    protected $casts = [
        'contacted_at'     => 'datetime',
        'next_action_date' => 'date',
    ];

    public function touchable()
    {
        return $this->morphTo();
    }
}
