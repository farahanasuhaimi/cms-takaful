<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShortLinkController extends Controller
{
    public function index()
    {
        $links = ShortLink::orderBy('section')->orderBy('code')->get()->groupBy('section');

        return view('short-links.index', compact('links'));
    }

    public function create()
    {
        return view('short-links.create', ['sections' => $this->sections()]);
    }

    public function store(Request $request)
    {
        ShortLink::create($this->validated($request) + ['user_id' => auth()->id()]);

        return redirect()->route('short-links.index')->with('success', 'Short link added.');
    }

    public function edit(ShortLink $shortLink)
    {
        abort_if($shortLink->user_id !== auth()->id(), 403);

        return view('short-links.edit', ['shortLink' => $shortLink, 'sections' => $this->sections()]);
    }

    public function update(Request $request, ShortLink $shortLink)
    {
        abort_if($shortLink->user_id !== auth()->id(), 403);

        $shortLink->update($this->validated($request, $shortLink));

        return redirect()->route('short-links.index')->with('success', 'Short link updated.');
    }

    public function destroy(ShortLink $shortLink)
    {
        abort_if($shortLink->user_id !== auth()->id(), 403);

        $shortLink->delete();

        return redirect()->route('short-links.index')->with('success', 'Short link deleted.');
    }

    private function validated(Request $request, ?ShortLink $ignore = null): array
    {
        // Accept a pasted full URL ("https://drtakaful.com/go/mc-quote") as well as the bare code.
        $request->merge([
            'code' => trim(preg_replace('#^.*?/go/#', '', (string) $request->input('code')), '/ '),
        ]);

        return $request->validate([
            'section' => 'required|string|max:100',
            'code'    => [
                'required', 'string', 'max:100', 'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('short_links', 'code')
                    ->where('user_id', auth()->id())
                    ->ignore($ignore?->id),
            ],
            'title'   => 'required|string|max:255',
            'target'  => 'nullable|string|max:255',
            'notes'   => 'nullable|string|max:5000',
        ], [
            'code.regex' => 'Use letters, numbers, - or _ only.',
        ]);
    }

    private function sections()
    {
        return ShortLink::select('section')->distinct()->orderBy('section')->pluck('section');
    }
}
