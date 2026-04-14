<x-app-layout>
    <x-slot name="title">Follow-up Log · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Follow-up Log</x-slot>

    {{-- Filter --}}
    <form method="GET" action="{{ route('touchpoints.index') }}" class="mb-5 flex items-center gap-3">
        <select name="channel" onchange="this.form.submit()"
                class="text-sm rounded-lg border-gray-200 focus:ring-matcha-400 focus:border-matcha-400">
            <option value="">All Channels</option>
            @foreach ($channels as $ch)
                <option value="{{ $ch }}" {{ request('channel') === $ch ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $ch)) }}
                </option>
            @endforeach
        </select>
        @if (request('channel'))
            <a href="{{ route('touchpoints.index') }}" class="text-xs text-gray-500 hover:text-gray-700">Clear filter</a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if ($touchpoints->count())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Person</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Channel</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Topic</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Next Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($touchpoints as $tp)
                        <tr class="hover:bg-matcha-50/30 transition">
                            <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                                {{ $tp->contacted_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3">
                                @if ($tp->touchable)
                                    @if ($tp->touchable_type === 'App\Models\Client')
                                        <a href="{{ route('clients.show', $tp->touchable_id) }}"
                                           class="text-sm font-medium text-gray-800 hover:text-matcha-600">
                                            {{ $tp->touchable->name }}
                                        </a>
                                        <span class="ml-1 text-xs text-matcha-400 bg-matcha-50 px-1.5 py-0.5 rounded">Client</span>
                                    @else
                                        <span class="text-sm font-medium text-gray-800">{{ $tp->touchable->name }}</span>
                                        <span class="ml-1 text-xs text-amber-500 bg-amber-50 px-1.5 py-0.5 rounded">Lead</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                    {{ ucfirst(str_replace('_', ' ', $tp->channel)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-700 max-w-xs truncate">{{ $tp->topic }}</td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                @if ($tp->next_action)
                                    {{ $tp->next_action }}
                                    @if ($tp->next_action_date)
                                        <span class="text-gray-400">({{ $tp->next_action_date->format('d M') }})</span>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($touchpoints->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $touchpoints->links() }}
                </div>
            @endif
        @else
            <div class="px-5 py-12 text-center">
                <p class="text-sm text-gray-400">No touchpoints logged yet. Log your first interaction from a client or lead page.</p>
            </div>
        @endif
    </div>

</x-app-layout>
