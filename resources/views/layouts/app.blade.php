<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Dr Takaful CMS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-matcha-50 font-sans text-gray-800">

{{-- App shell: sidebar + main --}}
<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-20 lg:hidden">
    </div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-56 bg-matcha-800 flex flex-col overflow-y-auto
                  transform -translate-x-full transition-transform duration-300 ease-in-out
                  lg:relative lg:translate-x-0 lg:flex-shrink-0 lg:z-auto"
           :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">

        {{-- Logo area --}}
        <div class="px-5 py-6 border-b border-matcha-900">
            <p class="text-white font-semibold text-lg leading-tight">Dr Takaful</p>
            <p class="text-matcha-200 text-xs mt-0.5">list.drtakaful.com</p>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-5">

            {{-- Overview --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Overview</p>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </div>

            {{-- Clients --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Clients</p>
                <a href="{{ route('clients.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('clients.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    My Policyholders
                </a>
                <a href="{{ route('leads.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('leads.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Warm &amp; Hot Leads
                </a>
                <a href="{{ route('touchpoints.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('touchpoints.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Follow-up Log
                </a>
            </div>

            {{-- Strategy --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Strategy</p>
                <a href="{{ route('angles.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('angles.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reach Angles
                </a>
            </div>

            {{-- Settings --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Settings</p>
                <a href="{{ route('plan-products.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('plan-products.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Plan Catalog
                </a>
                <a href="{{ route('settings.api') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('settings.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    API Settings
                </a>
            </div>

        </nav>

        {{-- Agent name at bottom --}}
        <div class="px-5 py-4 border-t border-matcha-900">
            <p class="text-matcha-200 text-xs">Hana · AIA Public Takaful</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="text-matcha-200/60 text-xs hover:text-matcha-100 transition">
                    Log out
                </button>
            </form>
        </div>

    </aside>

    {{-- Right side: topbar + content --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Topbar --}}
        <header class="h-14 bg-white border-b border-gray-200 flex items-center px-4 lg:px-6 gap-3 flex-shrink-0">

            {{-- Hamburger (mobile only) --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-1.5 text-gray-500 hover:text-gray-700 rounded-md hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Page title --}}
            <h1 class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                {{ $pageTitle ?? 'Dashboard' }}
            </h1>

            {{-- Search (desktop only — per-page search bars handle mobile) --}}
            <div class="flex-1 max-w-sm mx-auto hidden lg:block">
                <form method="GET" action="{{ route('clients.index') }}">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Search clients..."
                           class="w-full text-sm border-gray-200 rounded-lg px-3 py-1.5 focus:ring-matcha-400 focus:border-matcha-400" />
                </form>
            </div>

            {{-- Actions slot (context-sensitive "+ New" button) --}}
            <div class="ml-auto flex items-center gap-3">
                {{ $actions ?? '' }}

                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full bg-matcha-600 flex items-center justify-center text-white text-xs font-semibold">
                    HN
                </div>
            </div>

        </header>

        {{-- Success flash toast --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mx-6 mt-4 px-4 py-3 bg-matcha-100 text-matcha-800 border border-matcha-200 rounded-lg text-sm flex-shrink-0">
                {{ session('success') }}
            </div>
        @endif

        {{-- Main scrollable content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>

    </div>

</div>

</body>
</html>
