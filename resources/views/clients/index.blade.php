<x-app-layout>
    <x-slot name="title">Policyholders · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">My Policyholders</x-slot>
    <x-slot name="actions">
        <a href="{{ route('clients.create') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + New Client
        </a>
    </x-slot>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('clients.index') }}" class="mb-5">
        <div class="flex gap-2 max-w-sm">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Search by name or phone..."
                   class="flex-1 text-sm border-gray-200 rounded-lg focus:ring-matcha-400 focus:border-matcha-400" />
            <button type="submit"
                    class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm px-4 py-2 rounded-lg transition">
                Search
            </button>
            @if (request('q'))
                <a href="{{ route('clients.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg border border-gray-200 transition">
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @if ($clients->count())
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Plans</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Contacted</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Topic</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($clients as $client)
                        @php $last = $client->lastTouchpoint(); @endphp
                        <tr class="hover:bg-matcha-50/40 transition">
                            <td class="px-5 py-3">
                                <a href="{{ route('clients.show', $client) }}"
                                   class="font-medium text-gray-800 hover:text-matcha-600">
                                    {{ $client->name }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-gray-500">
                                @if ($client->phone)
                                    <a href="https://wa.me/{{ $client->phone }}" target="_blank"
                                       class="hover:text-green-600 transition">{{ $client->phone }}</a>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($client->policies as $policy)
                                        <span class="inline-block text-xs bg-matcha-50 text-matcha-700 rounded px-1.5 py-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $policy->plan_type)) }}
                                        </span>
                                    @empty
                                        <span class="text-gray-300 text-xs">No plans</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">
                                {{ $last ? $last->contacted_at->format('d M Y') : '—' }}
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs max-w-xs truncate">
                                {{ $last ? $last->topic : '—' }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('clients.show', $client) }}"
                                   class="text-xs text-matcha-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($clients->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $clients->links() }}
                </div>
            @endif
        @else
            <div class="px-5 py-12 text-center">
                <p class="text-sm text-gray-400">
                    @if (request('q'))
                        No clients found for "{{ request('q') }}".
                    @else
                        No clients yet. <a href="{{ route('clients.create') }}" class="text-matcha-600 hover:underline">Add your first policyholder.</a>
                    @endif
                </p>
            </div>
        @endif
    </div>

</x-app-layout>
