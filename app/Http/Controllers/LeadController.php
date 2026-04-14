<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $hotLeads = Lead::where('temperature', 'hot')
            ->whereNull('converted_at')
            ->orderBy('next_contact', 'asc')
            ->get();

        $warmLeads = Lead::where('temperature', 'warm')
            ->whereNull('converted_at')
            ->orderBy('next_contact', 'asc')
            ->get();

        return view('leads.index', compact('hotLeads', 'warmLeads'));
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

    public function convert(Lead $lead)
    {
        if ($lead->isConverted()) {
            return redirect()->route('leads.index')
                ->with('success', 'Lead was already converted.');
        }

        $client = Client::create([
            'name'  => $lead->name,
            'phone' => $lead->phone,
            'notes' => $lead->notes,
        ]);

        $lead->update(['converted_at' => now()]);

        return redirect()->route('clients.show', $client)
            ->with('success', "{$lead->name} converted to policyholder. Add their policies below.");
    }
}
