<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
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

        return view('dashboard.index', compact(
            'totalClients',
            'hotLeads',
            'warmLeads',
            'recentClients',
            'urgentLeads',
            'recentTouchpoints',
            'activeAngles',
        ));
    }
}
