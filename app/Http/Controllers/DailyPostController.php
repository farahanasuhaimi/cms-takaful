<?php

namespace App\Http\Controllers;

use App\Models\DailyPost;
use App\Models\ReachAngle;
use App\Services\DailyPostService;
use Illuminate\Http\Request;

class DailyPostController extends Controller
{
    public function index()
    {
        $posts  = DailyPost::orderByDesc('post_date')->orderByDesc('id')->paginate(20);
        $angles = ReachAngle::where('status', 'active')->orderBy('title')->get(['id', 'title', 'target_segment']);

        return view('daily-posts.index', compact('posts', 'angles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_date'      => ['required', 'date'],
            'platform'       => ['required', 'in:instagram,facebook,whatsapp,tiktok'],
            'topic'          => ['required', 'string', 'max:255'],
            'reach_angle_id' => ['nullable', 'exists:reach_angles,id'],
        ]);

        $post = DailyPost::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status'  => 'draft',
        ]);

        return redirect()->route('daily-posts.show', $post)->with('success', 'Post planned. Hit Generate to get your caption.');
    }

    public function show(DailyPost $dailyPost)
    {
        abort_if($dailyPost->user_id !== auth()->id(), 403);

        $dailyPost->load('reachAngle');
        $angles = ReachAngle::where('status', 'active')->orderBy('title')->get(['id', 'title', 'target_segment']);

        return view('daily-posts.show', ['post' => $dailyPost, 'angles' => $angles]);
    }

    public function generate(DailyPost $dailyPost, DailyPostService $service)
    {
        abort_if($dailyPost->user_id !== auth()->id(), 403);

        try {
            $service->generate($dailyPost);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Content generated.');
    }

    public function update(Request $request, DailyPost $dailyPost)
    {
        abort_if($dailyPost->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'status'         => ['sometimes', 'in:draft,ready,posted'],
            'caption'        => ['sometimes', 'nullable', 'string'],
            'post_date'      => ['sometimes', 'date'],
            'platform'       => ['sometimes', 'in:instagram,facebook,whatsapp,tiktok'],
            'topic'          => ['sometimes', 'string', 'max:255'],
            'reach_angle_id' => ['sometimes', 'nullable', 'exists:reach_angles,id'],
        ]);

        $dailyPost->update($validated);

        return back()->with('success', 'Post updated.');
    }

    public function destroy(DailyPost $dailyPost)
    {
        abort_if($dailyPost->user_id !== auth()->id(), 403);

        $dailyPost->delete();

        return redirect()->route('daily-posts.index')->with('success', 'Post deleted.');
    }
}
