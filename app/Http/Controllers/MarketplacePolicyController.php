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

    public function star(PlanProduct $product)
    {
        $product->loadCount('stars');

        $existing = MarketplacePolicyStar::where('user_id', auth()->id())
            ->where('plan_product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $starred = false;
        } else {
            MarketplacePolicyStar::create([
                'user_id'        => auth()->id(),
                'plan_product_id' => $product->id,
            ]);
            $starred = true;
        }

        $count = MarketplacePolicyStar::where('plan_product_id', $product->id)->count();

        return response()->json(['starred' => $starred, 'count' => $count]);
    }

    public function import(PlanProduct $product)
    {
        abort_if(! $product->is_shared, 403);

        PlanProduct::create([
            'user_id'               => auth()->id(),
            'plan_type'             => $product->plan_type,
            'name'                  => $product->name,
            'commission_first_year' => $product->commission_first_year,
            'attributes'            => $product->attributes,
            'notes'                 => $product->notes,
            'is_shared'             => false,
        ]);

        return redirect()->route('plan-products.index')
            ->with('success', "'{$product->name}' imported to your plan catalog.");
    }
}
