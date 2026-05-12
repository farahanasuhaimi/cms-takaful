<x-app-layout>
    <x-slot name="title">Invitations · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Admin — Invitations</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Generate invite form --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Generate Invite Link</h2>

                <form method="POST" action="{{ route('admin.invitations.store') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-600 mb-1">Friend's Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="friend@example.com"
                               class="w-full rounded-lg border-gray-200 text-sm focus:ring-matcha-400 focus:border-matcha-400 @error('email') border-red-400 @enderror" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="mt-3 w-full bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium py-2 rounded-lg transition">
                        Generate Link
                    </button>
                </form>

                {{-- Show generated link --}}
                @if (session('invite_link'))
                    <div class="mt-4 p-3 bg-matcha-50 border border-matcha-200 rounded-lg"
                         x-data="{ copied: false }">
                        <p class="text-xs font-medium text-matcha-700 mb-2">Share this link (expires in 7 days):</p>
                        <p class="text-xs text-matcha-600 break-all font-mono bg-white rounded p-2 border border-matcha-100 select-all">
                            {{ session('invite_link') }}
                        </p>
                        <button @click="navigator.clipboard.writeText('{{ session('invite_link') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="mt-2 text-xs text-matcha-600 hover:text-matcha-800 transition">
                            <span x-show="!copied">Copy link</span>
                            <span x-show="copied" x-cloak>Copied!</span>
                        </button>
                    </div>
                @endif

                <p class="mt-4 text-xs text-gray-400">
                    Links expire after 7 days. One invite per email address.
                </p>
            </div>
        </div>

        {{-- Invite history --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Invite History</h2>
                    <span class="text-xs text-gray-400">{{ $invitations->count() }} sent</span>
                </div>

                @if ($invitations->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm text-gray-400">No invites sent yet.</p>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50 text-left">
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Sent</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expires</th>
                                <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($invitations as $invitation)
                                <tr class="hover:bg-matcha-50/30 transition">
                                    <td class="px-5 py-3 font-medium text-gray-800">{{ $invitation->email }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $invitation->created_at->format('d M Y') }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $invitation->expires_at->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        @if ($invitation->used_at)
                                            <span class="inline-flex items-center gap-1 text-xs text-matcha-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-matcha-500 inline-block"></span>
                                                Accepted
                                            </span>
                                        @elseif ($invitation->expires_at->isPast())
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 inline-block"></span>
                                                Expired
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-amber-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        @if (! $invitation->used_at)
                                            <form method="POST" action="{{ route('admin.invitations.destroy', $invitation) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs text-red-500 hover:text-red-700 transition"
                                                        onclick="return confirm('Revoke this invite?')">
                                                    Revoke
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
