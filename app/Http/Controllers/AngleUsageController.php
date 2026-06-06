<?php

namespace App\Http\Controllers;

use App\Models\AngleUsage;
use App\Models\ReachAngle;
use Illuminate\Http\Request;

class AngleUsageController extends Controller
{
    public function store(Request $request, ReachAngle $angle)
    {
        $validated = $request->validate([
            'used_on'   => 'required|date',
            'lead_id'   => 'nullable|integer|exists:leads,id',
            'client_id' => 'nullable|integer|exists:clients,id',
            'notes'     => 'nullable|string|max:1000',
        ]);

        $angle->usages()->create(array_merge($validated, ['user_id' => auth()->id()]));

        return back()->with('success', 'Usage recorded.');
    }

    public function destroy(ReachAngle $angle, AngleUsage $usage)
    {
        abort_if($usage->user_id !== auth()->id(), 403);
        $usage->delete();

        return back()->with('success', 'Usage removed.');
    }
}
