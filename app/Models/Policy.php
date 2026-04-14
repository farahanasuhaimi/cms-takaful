<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'client_id', 'plan_product_id', 'plan_type', 'plan_name',
        'coverage_amount', 'start_date', 'frequency', 'premium_monthly', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function planProduct()
    {
        return $this->belongsTo(PlanProduct::class);
    }

    /**
     * Compute the next upcoming renewal date from today, based on start_date + frequency.
     * Monthly: same day-of-month each month.
     * Yearly:  same day-of-year each year.
     */
    public function nextRenewalDate(): ?Carbon
    {
        if (! $this->start_date || ! $this->frequency) {
            return null;
        }

        $today = now()->startOfDay();
        $start = $this->start_date->copy();

        if ($this->frequency === 'monthly') {
            // Same day of month; if that day has already passed this month, go next month
            $next = $today->copy()->day($start->day);
            if ($next->lt($today)) {
                $next->addMonthNoOverflow();
            }
        } else {
            // Yearly: same month + day; if already passed this year, go next year
            $next = $today->copy()->month($start->month)->day($start->day);
            if ($next->lt($today)) {
                $next->addYear();
            }
        }

        return $next;
    }
}
