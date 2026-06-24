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

        $attributes = [];
        $attributeOptions = [];
        $keys    = $request->input('attr_keys', []);
        $values  = $request->input('attr_values', []);
        $options = $request->input('attr_options', []);

        foreach ($keys as $i => $key) {
            $key = trim($key);
            if ($key !== '') {
                $attributes[$key] = trim($values[$i] ?? '');
                $optStr = trim($options[$i] ?? '');
                if ($optStr !== '') {
                    $attributeOptions[$key] = array_values(array_filter(array_map('trim', explode(',', $optStr))));
                }
            }
        }

        PlanProduct::create([
            'user_id'               => auth()->id(),
            'plan_type'             => $request->plan_type,
            'name'                  => $request->name,
            'commission_first_year' => $request->commission_first_year,
            'attributes'            => $attributes ?: null,
            'attribute_options'     => $attributeOptions ?: null,
            'notes'                 => $request->notes,
        ]);

        return redirect()->route('plan-products.index')
            ->with('success', 'Plan product registered.');
    }

    public function edit(PlanProduct $planProduct)
    {
        abort_if($planProduct->user_id !== auth()->id(), 403);

        return view('settings.plan-products.edit', compact('planProduct'));
    }

    public function update(Request $request, PlanProduct $planProduct)
    {
        abort_if($planProduct->user_id !== auth()->id(), 403);

        $request->validate([
            'plan_type'             => 'required|in:medical,critical_illness,personal_accident,group,hibah,income,other',
            'name'                  => 'required|string|max:255',
            'commission_first_year' => 'nullable|numeric|min:0|max:100',
            'notes'                 => 'nullable|string',
            'is_shared'             => 'nullable|boolean',
            'shared_note'           => 'nullable|string',
        ]);

        $attributes = [];
        $attributeOptions = [];
        $keys    = $request->input('attr_keys', []);
        $values  = $request->input('attr_values', []);
        $options = $request->input('attr_options', []);

        foreach ($keys as $i => $key) {
            $key = trim($key);
            if ($key !== '') {
                $attributes[$key] = trim($values[$i] ?? '');
                $optStr = trim($options[$i] ?? '');
                if ($optStr !== '') {
                    $attributeOptions[$key] = array_values(array_filter(array_map('trim', explode(',', $optStr))));
                }
            }
        }

        $planProduct->update([
            'plan_type'             => $request->plan_type,
            'name'                  => $request->name,
            'commission_first_year' => $request->commission_first_year,
            'attributes'            => $attributes ?: null,
            'attribute_options'     => $attributeOptions ?: null,
            'notes'                 => $request->notes,
            'is_shared'             => (bool) $request->input('is_shared', false),
            'shared_note'           => $request->shared_note,
        ]);

        return redirect()->route('plan-products.index')
            ->with('success', 'Plan product updated.');
    }

    public function destroy(PlanProduct $planProduct)
    {
        abort_if($planProduct->user_id !== auth()->id(), 403);

        $planProduct->delete();

        return redirect()->route('plan-products.index')
            ->with('success', 'Plan product removed.');
    }
}
