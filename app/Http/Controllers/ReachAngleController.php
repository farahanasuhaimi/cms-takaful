<?php

namespace App\Http\Controllers;

use App\Models\AngleContent;
use App\Models\Client;
use App\Models\ReachAngle;
use App\Services\AngleContentService;
use Illuminate\Http\Request;

class ReachAngleController extends Controller
{
    public function index()
    {
        $angles = ReachAngle::withCount('clients')
            ->with('pinnedContents')
            ->latest()
            ->get();

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

    public function generate(ReachAngle $angle, AngleContentService $service)
    {
        try {
            $contents = $service->generate($angle);
            return response()->json(['success' => true, 'contents' => $contents]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function pin(AngleContent $content, AngleContentService $service)
    {
        $content = $service->togglePin($content);
        return response()->json(['is_pinned' => $content->is_pinned]);
    }

    public function attachClient(ReachAngle $angle, Client $client)
    {
        if (! $angle->clients()->where('client_id', $client->id)->exists()) {
            $angle->clients()->attach($client->id);
        }

        return back()->with('success', "{$client->name} linked to this angle.");
    }
}
