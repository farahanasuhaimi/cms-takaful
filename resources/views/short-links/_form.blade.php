@php $link = $shortLink ?? null; @endphp

<div class="space-y-4">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section <span class="text-strawberry-500">*</span></label>
            <input type="text" id="section" name="section" list="section-options" required
                   value="{{ old('section', $link?->section) }}"
                   placeholder="e.g. Medical Card"
                   class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('section') border-red-400 @enderror" />
            <datalist id="section-options">
                @foreach ($sections as $section)
                    <option value="{{ $section }}"></option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-gray-400">Pick an existing one or type a new section.</p>
            @error('section') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Short code <span class="text-strawberry-500">*</span></label>
            <div class="flex rounded-lg shadow-sm">
                <span class="inline-flex items-center px-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-xs text-gray-500 whitespace-nowrap">drtakaful.com/go/</span>
                <input type="text" id="code" name="code" required
                       value="{{ old('code', $link?->code) }}"
                       placeholder="mc-quote"
                       class="w-full min-w-0 text-sm rounded-r-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('code') border-red-400 @enderror" />
            </div>
            @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-strawberry-500">*</span></label>
        <input type="text" id="title" name="title" required
               value="{{ old('title', $link?->title) }}"
               placeholder="What the page is about"
               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('title') border-red-400 @enderror" />
        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="target" class="block text-sm font-medium text-gray-700 mb-1">Page <span class="text-gray-400">(optional)</span></label>
        <input type="text" id="target" name="target"
               value="{{ old('target', $link?->target) }}"
               placeholder="medical-card-sebut-harga.html"
               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('target') border-red-400 @enderror" />
        @error('target') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
        <textarea id="notes" name="notes" rows="3"
                  placeholder="When to use it, which campaign, who it's for…"
                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('notes') border-red-400 @enderror">{{ old('notes', $link?->notes) }}</textarea>
        @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
        This list is for copying links. A new code only redirects on the website once it's also added to <code>url-map.php</code> in the drtakaful repo.
    </p>

</div>
