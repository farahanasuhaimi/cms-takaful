<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PlanProduct;
use App\Models\Policy;
use App\Models\Strategy;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $all = Client::with(['policies', 'touchpoints'])->latest('updated_at')->get();

        if ($request->filled('q')) {
            $q = strtolower($request->q);
            $all = $all->filter(fn($c) =>
                str_contains(strtolower($c->name ?? ''), $q) ||
                str_contains(strtolower($c->phone ?? ''), $q)
            )->values();
        }

        $perPage = 20;
        $page    = $request->input('page', 1);
        $clients = new LengthAwarePaginator(
            $all->slice(($page - 1) * $perPage, $perPage)->values(),
            $all->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'ic_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $client = Client::create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Policyholder added successfully.');
    }

    public function show(Client $client)
    {
        $client->load([
            'policies.planProduct',
            'reachAngles',
            'quotations',
        ]);

        $touchpoints = $client->touchpoints()->with('strategy')->paginate(10);
        $planProducts = PlanProduct::orderBy('plan_type')->orderBy('name')->get();
        $strategies = Strategy::where('user_id', auth()->id())->orderBy('title')->get(['id', 'title']);

        return view('clients.show', compact('client', 'touchpoints', 'planProducts', 'strategies'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'ic_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client removed.');
    }

    public function storePolicy(Request $request, Client $client)
    {
        $request->validate([
            'policy_number'   => 'nullable|string|max:100',
            'plan_product_id' => 'nullable|exists:plan_products,id',
            'plan_type'       => 'required|in:medical,critical_illness,personal_accident,group,hibah,income,other',
            'plan_name'       => 'nullable|string|max:255',
            'coverage_amount' => 'nullable|numeric|min:0',
            'start_date'      => 'nullable|date',
            'frequency'       => 'nullable|in:monthly,yearly',
            'premium_monthly' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        // If a catalog product is selected, derive plan_type and plan_name from it
        if ($request->filled('plan_product_id')) {
            $product = PlanProduct::find($request->plan_product_id);
            $planType = $product->plan_type;
            $planName = $product->name;
        } else {
            $planType = $request->plan_type;
            $planName = $request->plan_name;
        }

        $client->policies()->create([
            'user_id'         => auth()->id(),
            'policy_number'   => $request->policy_number ?: null,
            'plan_product_id' => $request->plan_product_id ?: null,
            'plan_type'       => $planType,
            'plan_name'       => $planName,
            'coverage_amount' => $request->coverage_amount,
            'start_date'      => $request->start_date,
            'frequency'       => $request->frequency,
            'premium_monthly' => $request->premium_monthly,
            'notes'           => $request->notes,
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Policy added.');
    }

    public function updatePolicy(Request $request, Client $client, Policy $policy)
    {
        abort_if($policy->client_id !== $client->id, 403);

        $request->validate([
            'policy_number'   => 'nullable|string|max:100',
            'plan_type'       => 'required|in:medical,critical_illness,personal_accident,group,hibah,income,other',
            'plan_name'       => 'nullable|string|max:255',
            'coverage_amount' => 'nullable|numeric|min:0',
            'start_date'      => 'nullable|date',
            'frequency'       => 'nullable|in:monthly,yearly',
            'premium_monthly' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $policy->update([
            'policy_number'   => $request->policy_number ?: null,
            'plan_type'       => $request->plan_type,
            'plan_name'       => $request->plan_name,
            'coverage_amount' => $request->coverage_amount,
            'start_date'      => $request->start_date,
            'frequency'       => $request->frequency,
            'premium_monthly' => $request->premium_monthly,
            'notes'           => $request->notes,
        ]);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Policy updated.');
    }

    public function destroyPolicy(Client $client, Policy $policy)
    {
        abort_if($policy->client_id !== $client->id, 403);

        $policy->delete();

        return redirect()->route('clients.show', $client)
            ->with('success', 'Policy removed.');
    }

    public function renewPolicy(Client $client, Policy $policy)
    {
        abort_if($policy->client_id !== $client->id, 403);

        if ($policy->start_date && $policy->frequency) {
            $newStart = $policy->frequency === 'monthly'
                ? $policy->start_date->copy()->addMonthNoOverflow()
                : $policy->start_date->copy()->addYear();

            $policy->update(['start_date' => $newStart]);
        }

        return back()->with('success', "{$client->name}'s policy marked as renewed.");
    }

    public function createRenewalTouchpoint(Client $client, Policy $policy)
    {
        abort_if($policy->client_id !== $client->id, 403);

        $label = $policy->plan_name ?? ucfirst(str_replace('_', ' ', $policy->plan_type));
        $renewal = $policy->nextRenewalDate();

        $client->touchpoints()->create([
            'user_id'          => auth()->id(),
            'contacted_at'     => now(),
            'channel'          => 'phone_call',
            'topic'            => "Policy renewal – {$label}",
            'next_action'      => 'Follow up on renewal confirmation',
            'next_action_date' => $renewal,
        ]);

        return back()->with('success', "Follow-up touchpoint created for {$client->name}.");
    }
}
