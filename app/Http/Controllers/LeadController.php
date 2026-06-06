<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FocusPoint;
use App\Models\Lead;
use App\Models\Strategy;
use App\Models\Touchpoint;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $hotLeads = Lead::with('focusPoints')
            ->where('temperature', 'hot')
            ->whereNull('converted_at')
            ->orderBy('next_contact', 'asc')
            ->get();

        $warmLeads = Lead::with('focusPoints')
            ->where('temperature', 'warm')
            ->whereNull('converted_at')
            ->orderBy('next_contact', 'asc')
            ->get();

        $strategies  = Strategy::where('user_id', auth()->id())->orderBy('title')->get(['id', 'title']);
        $focusPoints = FocusPoint::where('status', 'active')->orderBy('group')->orderBy('title')->get();

        return view('leads.index', compact('hotLeads', 'warmLeads', 'strategies', 'focusPoints'));
    }

    public function create()
    {
        return view('leads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'source'        => 'required|in:referral,social_media,cold_outreach,event,walk_in,other',
            'interest_area' => 'nullable|string|max:255',
            'temperature'   => 'required|in:hot,warm',
            'stage'         => 'required|in:new,contacted,presented,negotiating,stalled',
            'next_contact'  => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        Lead::create($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead added successfully.');
    }

    public function edit(Lead $lead)
    {
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'source'        => 'required|in:referral,social_media,cold_outreach,event,walk_in,other',
            'interest_area' => 'nullable|string|max:255',
            'temperature'   => 'required|in:hot,warm',
            'stage'         => 'required|in:new,contacted,presented,negotiating,stalled',
            'next_contact'  => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        $lead->update($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead removed.');
    }

    public function attachFocusPoint(Lead $lead, FocusPoint $focusPoint)
    {
        if (! $lead->focusPoints()->where('focus_point_id', $focusPoint->id)->exists()) {
            $lead->focusPoints()->attach($focusPoint->id);
        }

        if (request()->expectsJson()) {
            return response()->json(['tagged' => true]);
        }

        return back();
    }

    public function detachFocusPoint(Lead $lead, FocusPoint $focusPoint)
    {
        $lead->focusPoints()->detach($focusPoint->id);

        if (request()->expectsJson()) {
            return response()->json(['tagged' => false]);
        }

        return back();
    }

    public function convert(Request $request, Lead $lead)
    {
        if ($lead->isConverted()) {
            return redirect()->route('leads.index')
                ->with('success', 'Lead was already converted.');
        }

        $client = Client::create([
            'user_id' => auth()->id(),
            'lead_id' => $lead->id,
            'name'    => $lead->name,
            'phone'   => $lead->phone,
            'ic_no'   => $request->filled('ic_no') ? $request->input('ic_no') : null,
            'notes'   => $lead->notes,
        ]);

        // Migrate all touchpoints from the Lead to the new Client
        Touchpoint::where('touchable_type', Lead::class)
            ->where('touchable_id', $lead->id)
            ->where('user_id', auth()->id())
            ->update([
                'touchable_type' => Client::class,
                'touchable_id'   => $client->id,
            ]);

        $lead->update(['converted_at' => now()]);

        return redirect()->route('clients.show', $client)
            ->with('success', "{$lead->name} converted to policyholder. Contact history carried over. Add their policy below.");
    }
}
