<x-app-layout>
    <x-slot name="title">Credits · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">My Credits</x-slot>

    {{-- Balance card --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.736 6.979C9.208 6.193 9.696 6 10 6c.304 0 .792.193 1.264.979a1 1 0 001.715-1.029C12.279 4.784 11.232 4 10 4s-2.279.784-2.979 1.95c-.285.475-.507 1.003-.67 1.55H6a1 1 0 000 2h.013a9.358 9.358 0 000 1H6a1 1 0 100 2h.351c.163.547.385 1.075.67 1.55C7.721 15.216 8.768 16 10 16s2.279-.784 2.979-1.95a1 1 0 10-1.715-1.029C10.792 13.807 10.304 14 10 14c-.304 0-.792-.193-1.264-.979a4.265 4.265 0 01-.264-.521H10a1 1 0 100-2H8.017a7.36 7.36 0 010-1H10a1 1 0 100-2H8.472c.08-.185.167-.36.264-.521z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ auth()->user()->credits }}</p>
                <p class="text-xs text-gray-500">Available credits</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs text-gray-500 mb-1">Total earned</p>
            <p class="text-xl font-semibold text-matcha-700">
                {{ $transactions->where('amount', '>', 0)->sum('amount') }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 px-6 py-5">
            <p class="text-xs text-gray-500 mb-1">Total spent</p>
            <p class="text-xl font-semibold text-red-500">
                {{ abs($transactions->where('amount', '<', 0)->sum('amount')) }}
            </p>
        </div>
    </div>

    {{-- Transaction history --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Transaction History</h2>
        </div>

        @if ($transactions->isEmpty())
            <div class="px-5 py-12 text-center">
                <p class="text-sm text-gray-400">No transactions yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($transactions as $tx)
                    <div class="flex items-center px-5 py-3 hover:bg-gray-50/50 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">{{ $tx->description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $tx->created_at->format('d M Y, g:i A') }}</p>
                        </div>
                        <div class="flex-shrink-0 ml-4 text-right">
                            <span class="text-sm font-semibold {{ $tx->amount > 0 ? 'text-matcha-600' : 'text-red-500' }}">
                                {{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}
                            </span>
                            <p class="text-xs text-gray-400">{{ ucfirst($tx->type) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($transactions->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>

</x-app-layout>
