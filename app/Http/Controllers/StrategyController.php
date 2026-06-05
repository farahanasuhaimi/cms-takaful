<?php

namespace App\Http\Controllers;

use App\Models\FocusPoint;
use App\Models\Strategy;
use App\Models\StrategyStep;
use App\Services\StrategyAiService;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    public function index(Request $request)
    {
        $query = Strategy::where(function ($q) {
            $q->where('user_id', auth()->id())
              ->orWhereNull('user_id');
        })->where('status', '!=', 'removed');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('audience')) {
            $query->where('audience', $request->audience);
        }
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $strategies = $query->withCount('steps')->latest()->get();

        return view('strategies.index', compact('strategies'));
    }

    public function create(Request $request)
    {
        $angle = $request->filled('angle_id')
            ? \App\Models\ReachAngle::find($request->angle_id)
            : null;

        $focusPoints = FocusPoint::where('status', 'active')
            ->orderBy('group')->orderBy('title')
            ->get()->groupBy('group');

        return view('strategies.create', compact('angle', 'focusPoints'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category'    => 'required|in:prospecting,content,objection_handling,follow_up,referral,closing',
            'channel'     => 'required|in:whatsapp,instagram,facebook,face_to_face,general',
            'audience'    => 'required|in:strangers,warm_leads,family_friends,corporate,general',
            'difficulty'  => 'required|in:beginner,intermediate,advanced',
            'type'        => 'required|in:script,flow',
            'content'     => 'nullable|string',
        ]);

        $strategy = Strategy::create([
            ...$validated,
            'user_id' => auth()->id(),
            'source'  => 'self_made',
            'status'  => 'active',
        ]);

        $strategy->focusPoints()->sync($request->input('focus_point_ids', []));

        return redirect()->route('strategies.show', $strategy)
            ->with('success', 'Strategy created.');
    }

    public function show(Strategy $strategy)
    {
        abort_if(
            $strategy->user_id !== null && $strategy->user_id !== auth()->id(),
            403
        );

        $steps = $strategy->steps;
        $listing = $strategy->listing;
        $focusPoints = $strategy->focusPoints;

        return view('strategies.show', compact('strategy', 'steps', 'listing', 'focusPoints'));
    }

    public function edit(Strategy $strategy)
    {
        abort_if($strategy->user_id !== auth()->id(), 403);

        $steps = $strategy->steps;
        $focusPoints = FocusPoint::where('status', 'active')
            ->orderBy('group')->orderBy('title')
            ->get()->groupBy('group');
        $selectedFocusPointIds = $strategy->focusPoints->pluck('id')->toArray();

        return view('strategies.edit', compact('strategy', 'steps', 'focusPoints', 'selectedFocusPointIds'));
    }

    public function update(Request $request, Strategy $strategy)
    {
        abort_if($strategy->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category'    => 'required|in:prospecting,content,objection_handling,follow_up,referral,closing',
            'channel'     => 'required|in:whatsapp,instagram,facebook,face_to_face,general',
            'audience'    => 'required|in:strangers,warm_leads,family_friends,corporate,general',
            'difficulty'  => 'required|in:beginner,intermediate,advanced',
            'type'        => 'required|in:script,flow',
            'content'     => 'nullable|string',
        ]);

        $strategy->update($validated);

        $strategy->focusPoints()->sync($request->input('focus_point_ids', []));

        return redirect()->route('strategies.show', $strategy)
            ->with('success', 'Strategy updated.');
    }

    public function destroy(Strategy $strategy)
    {
        abort_if($strategy->user_id !== auth()->id(), 403);

        $strategy->update(['status' => 'removed']);

        return redirect()->route('strategies.index')
            ->with('success', 'Strategy removed.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'category'   => 'required|in:prospecting,content,objection_handling,follow_up,referral,closing',
            'channel'    => 'required|in:whatsapp,instagram,facebook,face_to_face,general',
            'audience'   => 'required|in:strangers,warm_leads,family_friends,corporate,general',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'type'       => 'required|in:script,flow',
            'brief'      => 'nullable|string|max:500',
        ]);

        try {
            $result = (new StrategyAiService())->generate($request->only(
                'category', 'channel', 'audience', 'difficulty', 'type', 'brief'
            ));
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function storeGenerated(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category'    => 'required|in:prospecting,content,objection_handling,follow_up,referral,closing',
            'channel'     => 'required|in:whatsapp,instagram,facebook,face_to_face,general',
            'audience'    => 'required|in:strangers,warm_leads,family_friends,corporate,general',
            'difficulty'  => 'required|in:beginner,intermediate,advanced',
            'type'        => 'required|in:script,flow',
            'content'     => 'nullable|string',
            'steps'       => 'nullable|array',
            'steps.*.title'       => 'required_with:steps|string|max:255',
            'steps.*.script'      => 'required_with:steps|string',
            'steps.*.timing_note' => 'nullable|string|max:255',
            'steps.*.branch_yes'  => 'nullable|string',
            'steps.*.branch_no'   => 'nullable|string',
        ]);

        $strategy = Strategy::create([
            'user_id'     => auth()->id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category'    => $validated['category'],
            'channel'     => $validated['channel'],
            'audience'    => $validated['audience'],
            'difficulty'  => $validated['difficulty'],
            'type'        => $validated['type'],
            'source'      => 'ai_guided',
            'content'     => $validated['content'] ?? null,
            'status'      => 'active',
        ]);

        if (! empty($validated['steps'])) {
            foreach ($validated['steps'] as $i => $step) {
                StrategyStep::create([
                    'strategy_id' => $strategy->id,
                    'step_order'  => $i + 1,
                    'title'       => $step['title'],
                    'script'      => $step['script'],
                    'timing_note' => $step['timing_note'] ?? null,
                    'branch_yes'  => $step['branch_yes'] ?? null,
                    'branch_no'   => $step['branch_no'] ?? null,
                ]);
            }
        }

        $strategy->focusPoints()->sync($request->input('focus_point_ids', []));

        return redirect()->route('strategies.show', $strategy)
            ->with('success', 'AI-guided strategy saved.');
    }

    public function storeStep(Request $request, Strategy $strategy)
    {
        abort_if($strategy->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'script'      => 'required|string',
            'timing_note' => 'nullable|string|max:255',
            'branch_yes'  => 'nullable|string',
            'branch_no'   => 'nullable|string',
        ]);

        $order = $strategy->steps()->max('step_order') + 1;

        StrategyStep::create([
            'strategy_id' => $strategy->id,
            'step_order'  => $order,
            ...$validated,
        ]);

        return back()->with('success', 'Step added.');
    }

    public function updateStep(Request $request, Strategy $strategy, StrategyStep $step)
    {
        abort_if($strategy->user_id !== auth()->id(), 403);
        abort_if($step->strategy_id !== $strategy->id, 403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'script'      => 'required|string',
            'timing_note' => 'nullable|string|max:255',
            'branch_yes'  => 'nullable|string',
            'branch_no'   => 'nullable|string',
        ]);

        $step->update($validated);

        return back()->with('success', 'Step updated.');
    }

    public function destroyStep(Strategy $strategy, StrategyStep $step)
    {
        abort_if($strategy->user_id !== auth()->id(), 403);
        abort_if($step->strategy_id !== $strategy->id, 403);

        $step->delete();

        $strategy->steps()->orderBy('step_order')->each(function ($s, $i) {
            $s->update(['step_order' => $i + 1]);
        });

        return back()->with('success', 'Step removed.');
    }
}
