<x-app-layout>
    <x-slot name="title">Strategy Library · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Strategy Library</x-slot>

    <x-slot name="actions">
        <a href="{{ route('strategies.create') }}"
           class="text-xs bg-matcha-600 hover:bg-matcha-700 text-white px-3 py-1.5 rounded-lg transition">
            + New Strategy
        </a>
    </x-slot>

    {{-- Filters --}}
    <form method="GET" action="{{ route('strategies.index') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap gap-3 items-end">

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Category</label>
            <select name="category" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All Categories</option>
                @foreach(['prospecting'=>'Prospecting','content'=>'Content','objection_handling'=>'Objection Handling','follow_up'=>'Follow Up','referral'=>'Referral','closing'=>'Closing'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('category') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Channel</label>
            <select name="channel" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All Channels</option>
                @foreach(['whatsapp'=>'WhatsApp','instagram'=>'Instagram','facebook'=>'Facebook','face_to_face'=>'Face to Face','general'=>'General'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('channel') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Audience</label>
            <select name="audience" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All Audiences</option>
                @foreach(['strangers'=>'Strangers','warm_leads'=>'Warm Leads','family_friends'=>'Family & Friends','corporate'=>'Corporate','general'=>'General'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('audience') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Difficulty</label>
            <select name="difficulty" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All Levels</option>
                <option value="beginner" @selected(request('difficulty') === 'beginner')>Beginner</option>
                <option value="intermediate" @selected(request('difficulty') === 'intermediate')>Intermediate</option>
                <option value="advanced" @selected(request('difficulty') === 'advanced')>Advanced</option>
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Type</label>
            <select name="type" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">Script + Flow</option>
                <option value="script" @selected(request('type') === 'script')>Script</option>
                <option value="flow" @selected(request('type') === 'flow')>Flow</option>
            </select>
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs text-gray-500">Source</label>
            <select name="source" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                <option value="">All Sources</option>
                <option value="provided" @selected(request('source') === 'provided')>Provided</option>
                <option value="ai_guided" @selected(request('source') === 'ai_guided')>AI Guided</option>
                <option value="self_made" @selected(request('source') === 'self_made')>Self Made</option>
            </select>
        </div>

        <button type="submit"
                class="text-xs bg-matcha-600 hover:bg-matcha-700 text-white px-3 py-1.5 rounded-lg transition">
            Filter
        </button>

        @if(request()->hasAny(['category','channel','audience','difficulty','type','source']))
            <a href="{{ route('strategies.index') }}"
               class="text-xs text-gray-400 hover:text-gray-600 py-1.5">Clear</a>
        @endif
    </form>

    @if ($strategies->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
            <p class="text-sm text-gray-400">No strategies found.</p>
            <p class="text-xs text-gray-400 mt-1">
                <a href="{{ route('strategies.create') }}" class="text-matcha-600 hover:underline">Create your first strategy</a>
                or check the <a href="{{ route('marketplace.strategies') }}" class="text-matcha-600 hover:underline">marketplace</a>.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($strategies as $strategy)
                <a href="{{ route('strategies.show', $strategy) }}"
                   class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col gap-3 hover:border-matcha-300 transition group">

                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-semibold text-gray-800 leading-snug group-hover:text-matcha-700 transition">
                            {{ $strategy->title }}
                        </p>
                        <span class="flex-shrink-0 text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $strategy->type === 'flow' ? 'bg-violet-100 text-violet-600' : 'bg-sky-100 text-sky-600' }}">
                            {{ ucfirst($strategy->type) }}
                        </span>
                    </div>

                    {{-- Description --}}
                    @if ($strategy->description)
                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-2">{{ $strategy->description }}</p>
                    @endif

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-1.5 mt-auto">
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ \App\Models\Strategy::categoryLabel($strategy->category) }}
                        </span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ \App\Models\Strategy::channelLabel($strategy->channel) }}
                        </span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                            {{ \App\Models\Strategy::audienceLabel($strategy->audience) }}
                        </span>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-2 border-t border-gray-50 text-xs text-gray-400">
                        <span class="capitalize">{{ $strategy->difficulty }}</span>
                        <div class="flex items-center gap-2">
                            @if ($strategy->type === 'flow')
                                <span>{{ $strategy->steps_count }} steps</span>
                            @endif
                            @if ($strategy->user_id === null)
                                <span class="text-amber-500 font-medium">Provided</span>
                            @elseif ($strategy->source === 'ai_guided')
                                <span class="text-matcha-500">AI</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</x-app-layout>
