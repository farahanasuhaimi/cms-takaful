<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FocusPoint extends Model
{
    protected $fillable = ['title', 'description', 'group', 'status'];

    public function strategies()
    {
        return $this->belongsToMany(Strategy::class, 'strategy_focus_point');
    }
}
