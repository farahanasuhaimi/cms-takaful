<?php

namespace App\Http\Controllers;

use App\Models\MarketplacePolicyStar;
use App\Models\PlanProduct;
use Illuminate\Http\Request;

class MarketplacePolicyController extends Controller
{
    public function index()
    {
        $products = PlanProduct::withoutGlobalScopes()
            ->with('user')
            ->withCount('stars')
            ->where('is_shared', true)
            ->orderByDesc('stars_count')
            ->orderByDesc('created_at')
            ->get();

        $starredIds = MarketplacePolicyStar::where('user_id', auth()->id())
            ->pluck('plan_product_id')
            ->flip();

        return view('marketplace.policies.index', compact('products', 'starredIds'));
    }

    public function star(int $product)
    {
        $planProduct = PlanProduct::withoutGlobalScopes()->findOrFail($product);
        abort_if(! $planProduct->is_shared, 403);

        $existing = MarketplacePolicyStar::where('user_id', auth()->id())
            ->where('plan_product_id', $planProduct->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $starred = false;
        } else {
            MarketplacePolicyStar::create([
                'user_id'         => auth()->id(),
                'plan_product_id' => $planProduct->id,
            ]);
            $starred = true;
        }

        $count = MarketplacePolicyStar::where('plan_product_id', $planProduct->id)->count();

        return response()->json(['starred' => $starred, 'count' => $count]);
    }

    public function import(int $product)
    {
        $planProduct = PlanProduct::withoutGlobalScopes()->findOrFail($product);
        abort_if(! $planProduct->is_shared, 403);

        PlanProduct::create([
            'user_id'               => auth()->id(),
            'plan_type'             => $planProduct->plan_type,
            'name'                  => $planProduct->name,
            'commission_first_year' => $planProduct->commission_first_year,
            'attributes'            => $planProduct->attributes,
            'notes'                 => $planProduct->notes,
            'is_shared'             => false,
        ]);

        return redirect()->route('plan-products.index')
            ->with('success', "'{$planProduct->name}' imported to your plan catalog.");
    }
}
