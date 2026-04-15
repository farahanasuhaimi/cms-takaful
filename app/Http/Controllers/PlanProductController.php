<?php

namespace App\Http\Controllers;

use App\Models\PlanProduct;
use Illuminate\Http\Request;

class PlanProductController extends Controller
{
    public function index()
    {
        $products = PlanProduct::orderBy('plan_type')->orderBy('name')->get()
            ->groupBy('plan_type');

        return view('settings.plan-products.index', compact('products'));
    }

    public function create()
    {
        return view('settings.plan-products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_type'             => 'required|in:medical,critical_illness,personal_accident,group,hibah,income,other',
            'name'                  => 'required|string|max:255',
            'commission_first_year' => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
        ]);

        // Build attributes array from parallel key/value arrays
        $attributes = [];
        $keys   = $request->input('attr_keys', []);
        $values = $request->input('attr_values', []);

        foreach ($keys as $i => $key) {
            $key = trim($key);
            if ($key !== '') {
                $attributes[$key] = trim($values[$i] ?? '');
            }
        }

        PlanProduct::create([
            'plan_type'             => $request->plan_type,
            'name'                  => $request->name,
            'commission_first_year' => $request->commission_first_year,
            'attributes'            => $attributes ?: null,
            'notes'                 => $request->notes,
        ]);

        return redirect()->route('plan-products.index')
            ->with('success', 'Plan product registered.');
    }

    public function edit(PlanProduct $planProduct)
    {
        return view('settings.plan-products.edit', compact('planProduct'));
    }

    public function update(Request $request, PlanProduct $planProduct)
    {
        $request->validate([
            'plan_type'             => 'required|in:medical,critical_illness,personal_accident,group,hibah,income,other',
            'name'                  => 'required|string|max:255',
            'commission_first_year' => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
        ]);

        $attributes = [];
        $keys   = $request->input('attr_keys', []);
        $values = $request->input('attr_values', []);

        foreach ($keys as $i => $key) {
            $key = trim($key);
            if ($key !== '') {
                $attributes[$key] = trim($values[$i] ?? '');
            }
        }

        $planProduct->update([
            'plan_type'             => $request->plan_type,
            'name'                  => $request->name,
            'commission_first_year' => $request->commission_first_year,
            'attributes'            => $attributes ?: null,
            'notes'                 => $request->notes,
        ]);

        return redirect()->route('plan-products.index')
            ->with('success', 'Plan product updated.');
    }

    public function destroy(PlanProduct $planProduct)
    {
        $planProduct->delete();

        return redirect()->route('plan-products.index')
            ->with('success', 'Plan product removed.');
    }
}
