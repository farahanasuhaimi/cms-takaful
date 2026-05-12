<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategyStep extends Model
{
    protected $fillable = [
        'strategy_id', 'step_order', 'title', 'script', 'timing_note', 'branch_yes', 'branch_no',
    ];

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }
}
