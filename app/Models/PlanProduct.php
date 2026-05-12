<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanProduct extends Model
{
    protected $fillable = ['user_id', 'plan_type', 'name', 'commission_first_year', 'attributes', 'notes', 'is_shared', 'shared_note'];

    protected $casts = [
        'attributes' => 'array',
        'is_shared'  => 'boolean',
    ];

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

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function stars()
    {
        return $this->hasMany(MarketplacePolicyStar::class);
    }

    public function starredByCurrentUser(): bool
    {
        return $this->stars()->where('user_id', auth()->id())->exists();
    }

    public function planTypeLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->plan_type));
    }
}
