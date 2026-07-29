<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'user_id', 'client_id', 'policy_number', 'plan_product_id', 'plan_type', 'plan_name',
        'coverage_amount', 'start_date', 'frequency', 'premium_monthly', 'notes', 'last_renewed_at',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'last_renewed_at'  => 'date',
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

        // The floor is normally "today", but if this policy was already
        // confirmed renewed, the next due date must be pushed past that
        // renewal instead of re-showing the same cycle as still due.
        $floor = now()->startOfDay();
        if ($this->last_renewed_at) {
            $afterRenewal = $this->last_renewed_at->copy()->startOfDay()->addDay();
            if ($afterRenewal->gt($floor)) {
                $floor = $afterRenewal;
            }
        }

        $anchorDay = $this->start_date->day;

        if ($this->frequency === 'monthly') {
            $next = $this->clampToAnchorDay($floor->copy(), $anchorDay);
            if ($next->lt($floor)) {
                $next = $this->clampToAnchorDay($next->addMonthNoOverflow(), $anchorDay);
            }
        } else {
            $next = $this->clampToAnchorDay($floor->copy()->month($this->start_date->month), $anchorDay);
            if ($next->lt($floor)) {
                $next = $this->clampToAnchorDay($next->addYear(), $anchorDay);
            }
        }

        return $next;
    }

    /**
     * Clamp a day-of-month to the anchor day, capped at the target month's
     * last day so e.g. an anchor of the 31st doesn't overflow into the
     * following month on a 28/29/30-day month.
     */
    private function clampToAnchorDay(Carbon $date, int $anchorDay): Carbon
    {
        $lastDayOfMonth = $date->copy()->endOfMonth()->day;

        return $date->day(min($anchorDay, $lastDayOfMonth));
    }
}
