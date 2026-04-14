<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Lead;
use App\Models\Touchpoint;
use Illuminate\Http\Request;

class TouchpointController extends Controller
{
    public function index(Request $request)
    {
        $query = Touchpoint::with('touchable')->latest('contacted_at');

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $touchpoints = $query->paginate(20)->withQueryString();

        $channels = [
            'whatsapp', 'phone_call', 'in_person',
            'dm_instagram', 'dm_facebook', 'email', 'other',
        ];

        return view('touchpoints.index', compact('touchpoints', 'channels'));
    }

    public function storeForClient(Request $request, Client $client)
    {
        $validated = $this->validateTouchpoint($request);
        $client->touchpoints()->create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Touchpoint logged.');
    }

    public function storeForLead(Request $request, Lead $lead)
    {
        $validated = $this->validateTouchpoint($request);
        $lead->touchpoints()->create($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Touchpoint logged.');
    }

    private function validateTouchpoint(Request $request): array
    {
        return $request->validate([
            'contacted_at'     => 'required|date',
            'channel'          => 'required|in:whatsapp,phone_call,in_person,dm_instagram,dm_facebook,email,other',
            'topic'            => 'required|string|max:255',
            'notes'            => 'nullable|string',
            'next_action'      => 'nullable|string|max:255',
            'next_action_date' => 'nullable|date',
        ]);
    }
}
