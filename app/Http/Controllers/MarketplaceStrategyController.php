<?php

namespace App\Http\Controllers;

use App\Models\AngleContent;
use App\Models\MarketplaceListing;
use App\Models\MarketplacePurchase;
use App\Models\ReachAngle;
use App\Services\CreditService;
use Illuminate\Http\Request;

class MarketplaceStrategyController extends Controller
{
    public function index()
    {
        $listings = MarketplaceListing::with(['seller', 'angleContent'])
            ->withCount('purchases')
            ->where('status', 'active')
            ->latest()
            ->get();

        $purchasedIds = MarketplacePurchase::where('buyer_user_id', auth()->id())
            ->pluck('listing_id')
            ->flip();

        return view('marketplace.strategies.index', compact('listings', 'purchasedIds'));
    }

    public function myListings()
    {
        $listings = MarketplaceListing::with('angleContent')
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
            'angle_content_id' => 'required|integer',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'price_credits'    => 'required|integer|min:1|max:500',
        ]);

        $content = AngleContent::findOrFail($request->angle_content_id);

        abort_if($content->user_id !== auth()->id(), 403);

        $alreadyListed = MarketplaceListing::where('angle_content_id', $content->id)
            ->where('status', 'active')
            ->exists();

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

        return back()->with('success', 'Strategy listed in the marketplace.');
    }

    public function buy(MarketplaceListing $listing)
    {
        abort_if($listing->status !== 'active', 404);
        abort_if($listing->seller_user_id === auth()->id(), 403, "You can't buy your own listing.");

        $alreadyBought = MarketplacePurchase::where('buyer_user_id', auth()->id())
            ->where('listing_id', $listing->id)
            ->exists();

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

        // Copy content to buyer's account under "Marketplace Imports" angle
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

        MarketplacePurchase::create([
            'buyer_user_id'      => auth()->id(),
            'listing_id'         => $listing->id,
            'credits_paid'       => $listing->price_credits,
            'imported_content_id' => $imported->id,
        ]);

        return redirect()->route('marketplace.strategies')
            ->with('success', "Strategy purchased! Find it in your Content Library under 'Marketplace Imports'.");
    }

    public function destroy(MarketplaceListing $listing)
    {
        abort_if($listing->seller_user_id !== auth()->id(), 403);

        $listing->update(['status' => 'removed']);

        return back()->with('success', 'Listing removed from marketplace.');
    }
}
