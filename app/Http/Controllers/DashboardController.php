<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\PlanProduct;
use App\Models\Policy;
use App\Models\ReachAngle;
use App\Models\Touchpoint;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();

        $hotLeads = Lead::where('temperature', 'hot')
            ->whereNull('converted_at')
            ->count();

        $warmLeads = Lead::where('temperature', 'warm')
            ->whereNull('converted_at')
            ->count();

        $recentClients = Client::with(['policies', 'touchpoints'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $urgentLeads = Lead::where('temperature', 'hot')
            ->whereNull('converted_at')
            ->orderBy('next_contact', 'asc')
            ->limit(5)
            ->get();

        $recentTouchpoints = Touchpoint::with('touchable')
            ->latest('contacted_at')
            ->limit(5)
            ->get();

        $activeAngles = ReachAngle::where('status', 'active')
            ->withCount('clients')
            ->get();

        // Top commission revenue — clients ranked by total estimated 1st-year commission
        $topCommissionClients = Client::with('policies.planProduct')->get()
            ->map(function ($client) {
                $client->total_commission = $client->policies
                    ->sum(fn ($p) => $p->estimatedCommissionFirstYear() ?? 0);
                return $client;
            })
            ->filter(fn ($c) => $c->total_commission > 0)
            ->sortByDesc('total_commission')
            ->take(5)
            ->values();

        $totalEstimatedCommission = $topCommissionClients->sum('total_commission');

        // Top plan conversion — plan products ranked by number of policies using them
        $topPlanProducts = PlanProduct::withCount('policies')
            ->orderByDesc('policies_count')
            ->limit(5)
            ->get()
            ->filter(fn ($p) => $p->policies_count > 0)
            ->values();

        $cutoff = now()->startOfDay()->addDays(30);
        $renewingSoon = Policy::with('client')
            ->whereNotNull('start_date')
            ->whereNotNull('frequency')
            ->get()
            ->map(function ($policy) {
                $policy->computed_renewal = $policy->nextRenewalDate();
                return $policy;
            })
            ->filter(fn($policy) => $policy->computed_renewal?->lte($cutoff))
            ->sortBy('computed_renewal')
            ->values();

        return view('dashboard.index', compact(
            'totalClients',
            'hotLeads',
            'warmLeads',
            'recentClients',
            'urgentLeads',
            'recentTouchpoints',
            'activeAngles',
            'renewingSoon',
            'topCommissionClients',
            'totalEstimatedCommission',
            'topPlanProducts',
        ));
    }
}
