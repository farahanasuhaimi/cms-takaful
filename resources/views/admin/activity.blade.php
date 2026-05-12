<x-app-layout>
    <x-slot name="title">Activity Log · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Admin — Activity Log</x-slot>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Recent Activity</h2>
            <span class="text-xs text-gray-400">All users · most recent first</span>
        </div>

        @if ($logs->isEmpty())
            <div class="px-5 py-12 text-center">
                <p class="text-sm text-gray-400">No activity recorded yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($logs as $log)
                    @php
                        $icon = match(true) {
                            str_starts_with($log->action, 'client')  => 'text-matcha-500',
                            str_starts_with($log->action, 'lead')    => 'text-amber-500',
                            str_starts_with($log->action, 'policy')  => 'text-blue-500',
                            $log->action === 'login'                 => 'text-gray-400',
                            default                                  => 'text-gray-400',
                        };
                        $dot = match(true) {
                            str_ends_with($log->action, '.deleted')  => 'bg-red-400',
                            str_ends_with($log->action, '.created')  => 'bg-matcha-400',
                            str_ends_with($log->action, '.converted')=> 'bg-strawberry-400',
                            $log->action === 'login'                 => 'bg-gray-300',
                            default                                  => 'bg-amber-400',
                        };
                    @endphp
                    <div class="flex items-start gap-4 px-5 py-3 hover:bg-gray-50/50 transition">

                        {{-- Dot --}}
                        <div class="mt-1.5 flex-shrink-0">
                            <span class="w-2 h-2 rounded-full {{ $dot }} inline-block"></span>
                        </div>

                        {{-- Description --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $log->user?->name ?? 'System' }}
                                &middot;
                                {{ $log->created_at->format('d M Y, g:i A') }}
                            </p>
                        </div>

                        {{-- Action badge --}}
                        <span class="flex-shrink-0 text-xs text-gray-400 font-mono">{{ $log->action }}</span>

                    </div>
                @endforeach
            </div>

            @if ($logs->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

</x-app-layout>
