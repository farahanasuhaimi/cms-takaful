<?php

namespace App\Http\Controllers;

use App\Models\AngleContent;
use App\Models\MarketplaceListing;
use App\Models\ReachAngle;
use App\Services\AngleContentService;

class AngleContentController extends Controller
{
    public function library()
    {
        $pinned = AngleContent::with('angle')
            ->where('is_pinned', true)
            ->latest()
            ->get()
            ->groupBy('angle_id');

        $listed    = MarketplaceListing::where('seller_user_id', auth()->id())
            ->where('status', 'active')
            ->whereNotNull('angle_content_id')
            ->pluck('angle_content_id')
            ->all();
        $listedIds = array_flip($listed);

        return view('angles.library', compact('pinned', 'listedIds'));
    }

    public function generate(ReachAngle $angle, AngleContentService $service)
    {
        try {
            $service->generate($angle);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Content generated for "' . $angle->title . '".');
    }

    public function pin(AngleContent $content, AngleContentService $service)
    {
        abort_if($content->user_id !== auth()->id(), 403);
        $service->togglePin($content);

        if (request()->expectsJson()) {
            return response()->json(['is_pinned' => $content->fresh()->is_pinned]);
        }

        return back();
    }
}
