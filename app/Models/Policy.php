<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'user_id', 'client_id', 'policy_number', 'plan_product_id', 'plan_type', 'plan_name',
        'coverage_amount', 'start_date', 'frequency', 'premium_monthly', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function planProduct()
    {
        return $this->belongsTo(PlanProduct::class);
    }

    public function estimatedCommissionFirstYear(): ?float
    {
        if (! $this->premium_monthly || ! $this->planProduct?->commission_first_year) {
            return null;
        }

        $annualPremium = $this->frequency === 'yearly'
            ? $this->premium_monthly
            : $this->premium_monthly * 12;

        return round($annualPremium * ($this->planProduct->commission_first_year / 100), 2);
    }

    public function nextRenewalDate(): ?Carbon
    {
        if (! $this->start_date || ! $this->frequency) {
            return null;
        }

        $today = now()->startOfDay();
        $start = $this->start_date->copy();

        if ($this->frequency === 'monthly') {
            $next = $today->copy()->day($start->day);
            if ($next->lt($today)) {
                $next->addMonthNoOverflow();
            }
        } else {
            $next = $today->copy()->month($start->month)->day($start->day);
            if ($next->lt($today)) {
                $next->addYear();
            }
        }

        return $next;
    }
}
