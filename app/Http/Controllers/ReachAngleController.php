<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ReachAngle;
use Illuminate\Http\Request;

class ReachAngleController extends Controller
{
    public function index()
    {
        $angles = ReachAngle::withCount('clients')->latest()->get();

        return view('angles.index', compact('angles'));
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
            'status'         => 'required|in:active,paused,archived',
        ]);

        ReachAngle::create($validated);

        return redirect()->route('angles.index')
            ->with('success', 'Reach angle created.');
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
            'status'         => 'required|in:active,paused,archived',
        ]);

        $angle->update($validated);

        return redirect()->route('angles.index')
            ->with('success', 'Angle updated.');
    }

    public function destroy(ReachAngle $angle)
    {
        $angle->delete();

        return redirect()->route('angles.index')
            ->with('success', 'Angle removed.');
    }

    public function attachClient(ReachAngle $angle, Client $client)
    {
        if (! $angle->clients()->where('client_id', $client->id)->exists()) {
            $angle->clients()->attach($client->id);
        }

        return back()->with('success', "{$client->name} linked to this angle.");
    }
}
