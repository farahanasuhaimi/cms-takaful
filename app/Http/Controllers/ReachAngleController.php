<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\ReachAngle;
use App\Models\Strategy;
use Illuminate\Http\Request;

class ReachAngleController extends Controller
{
    public function index()
    {
        $angles = ReachAngle::with(['clients', 'leads', 'strategies', 'latestContents', 'usages.lead', 'usages.client'])
            ->latest()
            ->get();

        $allLeads      = Lead::orderBy('id', 'desc')->get(['id', 'name']);
        $allClients    = Client::orderBy('id', 'desc')->get(['id', 'name']);
        $allStrategies = Strategy::orderBy('title')->get(['id', 'title', 'category']);

        return view('angles.index', compact('angles', 'allLeads', 'allClients', 'allStrategies'));
    }

    public function create()
    {
        return view('angles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'target_segment' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:active,paused,archived',
        ]);

        $validated['user_id'] = auth()->id();
        ReachAngle::create($validated);

        return redirect()->route('angles.index')->with('success', 'Reach angle created.');
    }

    public function edit(ReachAngle $angle)
    {
        return view('angles.edit', compact('angle'));
    }

    public function update(Request $request, ReachAngle $angle)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'target_segment' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:active,paused,archived',
        ]);

        $angle->update($validated);

        return redirect()->route('angles.index')->with('success', 'Angle updated.');
    }

    public function destroy(ReachAngle $angle)
    {
        $angle->delete();

        return redirect()->route('angles.index')->with('success', 'Angle removed.');
    }

    // --- Lead linking ---

    public function attachLead(ReachAngle $angle, Lead $lead)
    {
        if (! $angle->leads()->where('lead_id', $lead->id)->exists()) {
            $angle->leads()->attach($lead->id);
        }

        return back()->with('success', "{$lead->name} linked to angle.");
    }

    public function detachLead(ReachAngle $angle, Lead $lead)
    {
        $angle->leads()->detach($lead->id);

        return back()->with('success', 'Lead removed from angle.');
    }

    // --- Client linking ---

    public function attachClient(ReachAngle $angle, Client $client)
    {
        if (! $angle->clients()->where('client_id', $client->id)->exists()) {
            $angle->clients()->attach($client->id);
        }

        return back()->with('success', "{$client->name} linked to angle.");
    }

    public function detachClient(ReachAngle $angle, Client $client)
    {
        $angle->clients()->detach($client->id);

        return back()->with('success', 'Client removed from angle.');
    }

    // --- Strategy linking ---

    public function attachStrategy(ReachAngle $angle, Strategy $strategy)
    {
        if (! $angle->strategies()->where('strategy_id', $strategy->id)->exists()) {
            $angle->strategies()->attach($strategy->id);
        }

        return back()->with('success', "Strategy linked to angle.");
    }

    public function detachStrategy(ReachAngle $angle, Strategy $strategy)
    {
        $angle->strategies()->detach($strategy->id);

        return back()->with('success', 'Strategy removed from angle.');
    }
}
