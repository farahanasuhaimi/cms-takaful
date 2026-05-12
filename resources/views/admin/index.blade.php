<x-app-layout>
    <x-slot name="title">Admin · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Admin — User Management</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.invitations.index') }}"
           class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Invite User
        </a>
    </x-slot>

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">All Users</h2>
            <span class="text-xs text-gray-400">{{ $users->count() }} account{{ $users->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Clients</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Leads</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($users as $user)
                        <tr class="hover:bg-matcha-50/30 transition {{ ! $user->is_active ? 'opacity-50' : '' }}">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </td>
                            <td class="px-5 py-3">
                                @if ($user->is_admin)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-strawberry-100 text-strawberry-700">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                        Agent
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center text-gray-600 font-medium">{{ $user->clients_count }}</td>
                            <td class="px-5 py-3 text-center text-gray-600 font-medium">{{ $user->leads_count }}</td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center gap-1 text-xs text-matcha-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-matcha-500 inline-block"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 inline-block"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if (! $user->is_admin)
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs {{ $user->is_active ? 'text-red-500 hover:text-red-700' : 'text-matcha-600 hover:text-matcha-800' }} transition">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>
