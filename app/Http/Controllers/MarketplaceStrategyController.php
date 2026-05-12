<?php

namespace App\Http\Controllers;

use App\Models\AngleContent;
use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\ReachAngle;
use App\Models\Strategy;
use App\Models\StrategyStep;
use App\Services\CreditService;
use Illuminate\Http\Request;

class MarketplaceStrategyController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketplaceListing::with(['seller', 'angleContent', 'strategy'])
            ->withCount('purchases')
            ->where('status', 'active')
            ->latest();

        if ($request->filled('category')) {
            $query->whereHas('strategy', fn($q) => $q->where('category', $request->category));
        }
        if ($request->filled('channel')) {
            $query->whereHas('strategy', fn($q) => $q->where('channel', $request->channel));
        }
        if ($request->filled('audience')) {
            $query->whereHas('strategy', fn($q) => $q->where('audience', $request->audience));
        }
        if ($request->filled('difficulty')) {
            $query->whereHas('strategy', fn($q) => $q->where('difficulty', $request->difficulty));
        }
        if ($request->filled('type')) {
            $query->whereHas('strategy', fn($q) => $q->where('type', $request->type));
        }

        $listings = $query->get();

        $purchasedIds = MarketplacePurchase::where('buyer_user_id', auth()->id())
            ->pluck('listing_id')
            ->flip();

        return view('marketplace.strategies.index', compact('listings', 'purchasedIds'));
    }

    public function myListings()
    {
        $listings = MarketplaceListing::with(['angleContent', 'strategy'])
            ->withCount('purchases')
            ->where('seller_user_id', auth()->id())
            ->latest()
            ->get();

        $totalEarned = $listings->sum(fn($l) => $l->purchases_count * $l->price_credits);

        return view('marketplace.strategies.my-listings', compact('listings', 'totalEarned'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'strategy_id'   => 'nullable|integer',
            'angle_content_id' => 'nullable|integer',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'price_credits' => 'required|integer|min:1|max:500',
        ]);

        if ($request->filled('strategy_id')) {
            $strategy = Strategy::findOrFail($request->strategy_id);
            abort_if($strategy->user_id !== auth()->id(), 403);

            $alreadyListed = MarketplaceListing::where('strategy_id', $strategy->id)
                ->where('status', 'active')->exists();

            if ($alreadyListed) {
                return back()->with('error', 'This strategy is already listed in the marketplace.');
            }

            MarketplaceListing::create([
                'seller_user_id' => auth()->id(),
                'strategy_id'    => $strategy->id,
                'title'          => $request->title,
                'description'    => $request->description,
                'price_credits'  => $request->price_credits,
            ]);
        } else {
            $content = AngleContent::findOrFail($request->angle_content_id);
            abort_if($content->user_id !== auth()->id(), 403);

            $alreadyListed = MarketplaceListing::where('angle_content_id', $content->id)
                ->where('status', 'active')->exists();

            if ($alreadyListed) {
                return back()->with('error', 'This content is already listed in the marketplace.');
            }

            MarketplaceListing::create([
                'seller_user_id'   => auth()->id(),
                'angle_content_id' => $content->id,
                'title'            => $request->title,
                'description'      => $request->description,
                'price_credits'    => $request->price_credits,
            ]);
        }

        return back()->with('success', 'Strategy listed in the marketplace.');
    }

    public function buy(MarketplaceListing $listing)
    {
        abort_if($listing->status !== 'active', 404);
        abort_if($listing->seller_user_id === auth()->id(), 403, "You can't buy your own listing.");

        $alreadyBought = MarketplacePurchase::where('buyer_user_id', auth()->id())
            ->where('listing_id', $listing->id)->exists();

        if ($alreadyBought) {
            return back()->with('error', 'You have already purchased this strategy.');
        }

        $buyer  = auth()->user();
        $seller = $listing->seller;

        $spent = CreditService::spend($buyer, $listing->price_credits, "Bought strategy: {$listing->title}");

        if (! $spent) {
            return back()->with('error', "Insufficient credits. You need {$listing->price_credits} credits.");
        }

        CreditService::award($seller, $listing->price_credits, 'sale', "Sold strategy: {$listing->title}");

        $purchaseData = [
            'buyer_user_id' => auth()->id(),
            'listing_id'    => $listing->id,
            'credits_paid'  => $listing->price_credits,
        ];

        if ($listing->strategy_id) {
            $original = $listing->strategy;
            $copy = Strategy::create([
                'user_id'     => auth()->id(),
                'title'       => $original->title,
                'description' => $original->description,
                'category'    => $original->category,
                'channel'     => $original->channel,
                'audience'    => $original->audience,
                'difficulty'  => $original->difficulty,
                'type'        => $original->type,
                'source'      => 'provided',
                'content'     => $original->content,
                'status'      => 'active',
            ]);

            foreach ($original->steps as $step) {
                StrategyStep::create([
                    'strategy_id' => $copy->id,
                    'step_order'  => $step->step_order,
                    'title'       => $step->title,
                    'script'      => $step->script,
                    'timing_note' => $step->timing_note,
                    'branch_yes'  => $step->branch_yes,
                    'branch_no'   => $step->branch_no,
                ]);
            }

            $purchaseData['imported_strategy_id'] = $copy->id;
        } else {
            $importAngle = ReachAngle::firstOrCreate(
                ['user_id' => auth()->id(), 'title' => 'Marketplace Imports'],
                ['description' => 'Strategies purchased from the marketplace', 'status' => 'active', 'user_id' => auth()->id()]
            );

            $original = $listing->angleContent;
            $imported = AngleContent::create([
                'user_id'   => auth()->id(),
                'angle_id'  => $importAngle->id,
                'batch'     => 1,
                'style'     => $original->style,
                'content'   => $original->content,
                'is_pinned' => true,
                'model'     => $original->model,
            ]);

            $purchaseData['imported_content_id'] = $imported->id;
        }

        MarketplacePurchase::create($purchaseData);

        $destination = $listing->strategy_id
            ? route('strategies.show', $listing->strategy_id)
            : route('marketplace.strategies');

        return redirect($destination)
            ->with('success', "Strategy purchased! Find it in your Strategy Library.");
    }

    public function destroy(MarketplaceListing $listing)
    {
        abort_if($listing->seller_user_id !== auth()->id(), 403);

        $listing->update(['status' => 'removed']);

        return back()->with('success', 'Listing removed from marketplace.');
    }
}
