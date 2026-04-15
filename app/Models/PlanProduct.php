<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanProduct extends Model
{
    protected $fillable = ['plan_type', 'name', 'commission_first_year', 'attributes', 'notes'];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function planTypeLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->plan_type));
    }
}
