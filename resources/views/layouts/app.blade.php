<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $title ?? 'Dr Takaful CMS' }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-matcha-50 font-sans text-gray-800">

{{-- App shell: sidebar + main --}}
<div class="app-shell flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

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
                <a href="{{ route('tasks.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('tasks.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6m-6 4h4" />
                    </svg>
                    Task Board
                </a>
            </div>

            {{-- Clients --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Sales</p>
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
                <a href="{{ route('quotations.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('quotations.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Quotations
                </a>
            </div>

            {{-- Content --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Content</p>
                <a href="{{ route('daily-posts.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('daily-posts.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Daily Posts
                </a>
                <a href="{{ route('short-links.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('short-links.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Short Links
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
                <a href="{{ route('strategies.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('strategies.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Strategy Library
                </a>
            </div>

            {{-- Marketplace --}}
            <div>
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Marketplace</p>
                <a href="{{ route('marketplace.strategies') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('marketplace.strategies*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Strategies
                </a>
                <a href="{{ route('marketplace.policies') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('marketplace.policies*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Policies
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

        {{-- Admin links --}}
        @if (auth()->user()?->is_admin)
            <div class="px-3 pb-2">
                <p class="px-2 text-matcha-200 text-xs font-semibold uppercase tracking-wider mb-1">Admin</p>
                <a href="{{ route('admin.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('admin.index') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Users
                </a>
                <a href="{{ route('admin.invitations.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('admin.invitations.*') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Invitations
                </a>
                <a href="{{ route('admin.activity') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition
                          {{ request()->routeIs('admin.activity') ? 'bg-white/10 text-white border-l-2 border-strawberry-400' : 'text-matcha-100 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Activity Log
                </a>
            </div>
        @endif

        {{-- User + credits + logout at bottom --}}
        <div class="px-5 py-4 border-t border-matcha-900">
            <p class="text-matcha-100 text-xs font-medium">{{ auth()->user()?->name }}</p>
            <p class="text-matcha-200/60 text-xs truncate">{{ auth()->user()?->email }}</p>
            <a href="{{ route('account.credits') }}"
               class="mt-1.5 inline-flex items-center gap-1 text-xs text-amber-300 hover:text-amber-200 transition">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.736 6.979C9.208 6.193 9.696 6 10 6c.304 0 .792.193 1.264.979a1 1 0 001.715-1.029C12.279 4.784 11.232 4 10 4s-2.279.784-2.979 1.95c-.285.475-.507 1.003-.67 1.55H6a1 1 0 000 2h.013a9.358 9.358 0 000 1H6a1 1 0 100 2h.351c.163.547.385 1.075.67 1.55C7.721 15.216 8.768 16 10 16s2.279-.784 2.979-1.95a1 1 0 10-1.715-1.029C10.792 13.807 10.304 14 10 14c-.304 0-.792-.193-1.264-.979a4.265 4.265 0 01-.264-.521H10a1 1 0 100-2H8.017a7.36 7.36 0 010-1H10a1 1 0 100-2H8.472c.08-.185.167-.36.264-.521z"/>
                </svg>
                {{ auth()->user()?->credits ?? 0 }} credits
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1.5">
                @csrf
                <button type="submit" class="text-matcha-200/60 text-xs hover:text-matcha-100 transition">
                    Log out
                </button>
            </form>
        </div>

    </aside>

    {{-- Right side: topbar + content --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Topbar — on mobile the actions slot wraps onto its own row below the title --}}
        <header class="min-h-14 lg:h-14 bg-white border-b border-gray-200 flex flex-wrap lg:flex-nowrap items-center px-3 sm:px-4 lg:px-6 py-2 lg:py-0 gap-x-2 sm:gap-x-3 gap-y-2 flex-shrink-0">

            {{-- Hamburger (mobile only) --}}
            <button @click="sidebarOpen = !sidebarOpen" aria-label="Open menu"
                    class="lg:hidden -ml-1 p-2 text-gray-500 hover:text-gray-700 rounded-md hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Page title --}}
            <h1 class="min-w-0 flex-1 lg:flex-none truncate text-sm font-semibold text-gray-700">
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

            {{-- Privacy Mode toggle — blurs client names/commission/payment for screenshots --}}
            <button type="button" x-data @click="$store.privacy.toggle()"
                    :class="$store.privacy.enabled
                        ? 'bg-strawberry-100 text-strawberry-700 border-strawberry-200'
                        : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-gray-100'"
                    :aria-pressed="$store.privacy.enabled"
                    class="ml-auto flex-shrink-0 flex items-center gap-1.5 text-xs font-medium px-2.5 sm:px-3 py-2 sm:py-1.5 rounded-lg border transition"
                    title="Blur client names, commission & payment amounts before screenshotting for social media (PDPA)"
                    aria-label="Privacy Mode">
                <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="hidden sm:inline" x-text="$store.privacy.enabled ? 'Privacy On' : 'Privacy Mode'"></span>
            </button>

            {{-- Actions slot (context-sensitive "+ New" button) — full-width second row on mobile --}}
            @isset($actions)
                <div class="order-last basis-full lg:order-none lg:basis-auto flex flex-wrap items-center gap-2 lg:gap-3">
                    {{ $actions }}
                </div>
            @endisset

            {{-- Avatar --}}
            @php
                $initials = collect(explode(' ', auth()->user()?->name ?? ''))
                    ->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
            @endphp
            <div class="hidden lg:flex flex-shrink-0 w-8 h-8 rounded-full bg-matcha-600 items-center justify-center text-white text-xs font-semibold"
                 title="{{ auth()->user()?->name }}">
                {{ $initials }}
            </div>

        </header>

        {{-- Success flash toast --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mx-4 sm:mx-6 mt-4 px-4 py-3 bg-matcha-100 text-matcha-800 border border-matcha-200 rounded-lg text-sm flex-shrink-0">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error flash toast --}}
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mx-4 sm:mx-6 mt-4 px-4 py-3 bg-strawberry-100 text-strawberry-800 border border-strawberry-200 rounded-lg text-sm flex-shrink-0">
                {{ session('error') }}
            </div>
        @endif

        {{-- Main scrollable content — extra bottom padding on mobile clears the tab bar --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 pb-24 sm:pb-24 lg:pb-6">
            {{ $slot }}
        </main>

        {{-- Bottom tab bar (mobile only) --}}
        @php
            $tabs = [
                ['route' => 'dashboard',      'match' => 'dashboard',  'label' => 'Home',    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['route' => 'tasks.index',    'match' => 'tasks.*',    'label' => 'Tasks',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6m-6 4h4'],
                ['route' => 'clients.index',  'match' => 'clients.*',  'label' => 'Clients', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['route' => 'leads.index',    'match' => 'leads.*',    'label' => 'Leads',   'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ];
        @endphp
        <nav class="lg:hidden print:hidden fixed bottom-0 inset-x-0 z-10 bg-white border-t border-gray-200 pb-[env(safe-area-inset-bottom)]"
             aria-label="Quick navigation">
            <div class="grid grid-cols-5">
                @foreach ($tabs as $tab)
                    @php $active = request()->routeIs($tab['match']); @endphp
                    <a href="{{ route($tab['route']) }}"
                       class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium transition
                              {{ $active ? 'text-matcha-800' : 'text-gray-400 hover:text-gray-600' }}"
                       @if ($active) aria-current="page" @endif>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}" />
                        </svg>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
                <button type="button" @click="sidebarOpen = true"
                        class="flex flex-col items-center justify-center gap-0.5 py-2 text-[11px] font-medium text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Menu
                </button>
            </div>
        </nav>

    </div>

</div>

</body>
</html>
