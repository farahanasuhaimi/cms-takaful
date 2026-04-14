<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PlanProduct;
use App\Models\Policy;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['policies', 'touchpoints']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $clients = $query->latest('updated_at')->paginate(20)->withQueryString();

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

        $client = Client::create($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Policyholder added successfully.');
    }

    public function show(Client $client)
    {
        $client->load([
            'policies.planProduct',
            'reachAngles',
        ]);

        $touchpoints = $client->touchpoints()->paginate(10);
        $planProducts = PlanProduct::orderBy('plan_type')->orderBy('name')->get();

        return view('clients.show', compact('client', 'touchpoints', 'planProducts'));
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

    public function destroyPolicy(Client $client, Policy $policy)
    {
        $policy->delete();

        return redirect()->route('clients.show', $client)
            ->with('success', 'Policy removed.');
    }
}
