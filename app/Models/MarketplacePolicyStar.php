<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplacePolicyStar extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'plan_product_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planProduct()
    {
        return $this->belongsTo(PlanProduct::class);
    }
}
