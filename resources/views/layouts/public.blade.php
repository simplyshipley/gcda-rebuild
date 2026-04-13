<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Greater Chattanooga Darting Association — standings, stats, and schedules">
    <title>{{ $title ?? 'GCDA' }} — Greater Chattanooga Darting Association</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full antialiased" style="background-color: #111827; color: #f9fafb;">

    {{-- Skip navigation — WCAG 2.4.1 (G-066) --}}
    <a href="#main-content" class="skip-nav">Skip to main content</a>

    {{-- Top navigation --}}
    <header role="banner">
        <nav aria-label="Main navigation" class="border-b" style="background-color: #1f2937; border-color: #374151;">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    {{-- Logo / Brand --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="GCDA home">
                            {{-- Dartboard SVG logo (green brand color) --}}
                            <svg class="h-9 w-9" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                                <circle cx="18" cy="18" r="17" stroke="#71a100" stroke-width="2" fill="#111827"/>
                                <circle cx="18" cy="18" r="12" stroke="#71a100" stroke-width="1.5" fill="#1a2a00"/>
                                <circle cx="18" cy="18" r="7" stroke="#71a100" stroke-width="1.5" fill="#111827"/>
                                <circle cx="18" cy="18" r="3" fill="#71a100"/>
                                <line x1="18" y1="1" x2="18" y2="35" stroke="#71a100" stroke-width="0.5" opacity="0.4"/>
                                <line x1="1" y1="18" x2="35" y2="18" stroke="#71a100" stroke-width="0.5" opacity="0.4"/>
                            </svg>
                            <span class="text-xl font-bold tracking-tight" style="color: #71a100;">GCDA</span>
                        </a>
                        <span class="hidden sm:block text-sm" style="color: #6b7280;">Greater Chattanooga Darting Association</span>
                    </div>

                    {{-- Desktop nav links --}}
                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('standings') }}" class="px-3 py-2 rounded text-sm font-medium transition-colors hover:text-white {{ request()->routeIs('standings*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:bg-gray-700' }}">
                            Standings
                        </a>
                        <a href="{{ route('schedule') }}" class="px-3 py-2 rounded text-sm font-medium transition-colors hover:text-white {{ request()->routeIs('schedule*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:bg-gray-700' }}">
                            Schedule
                        </a>
                        <a href="{{ route('big-shots') }}" class="px-3 py-2 rounded text-sm font-medium transition-colors hover:text-white {{ request()->routeIs('big-shots*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:bg-gray-700' }}">
                            Big Shots
                        </a>
                        <a href="{{ route('players') }}" class="px-3 py-2 rounded text-sm font-medium transition-colors hover:text-white {{ request()->routeIs('players*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:bg-gray-700' }}">
                            Players
                        </a>
                        <a href="{{ route('venues') }}" class="px-3 py-2 rounded text-sm font-medium transition-colors hover:text-white {{ request()->routeIs('venues*') ? 'text-white bg-gray-700' : 'text-gray-300 hover:bg-gray-700' }}">
                            Venues
                        </a>
                    </div>

                    {{-- Mobile menu button --}}
                    <div class="md:hidden">
                        <button type="button" x-data="{ open: false }" @click="open = !open"
                            class="inline-flex items-center justify-center rounded p-2 text-gray-400 hover:text-white hover:bg-gray-700"
                            aria-label="Open navigation menu">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        </nav>
    </header>

    {{-- Main content --}}
    <main id="main-content" role="main">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer role="contentinfo" class="mt-16 border-t py-8" style="background-color: #1f2937; border-color: #374151;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm" style="color: #6b7280;">
                    &copy; {{ date('Y') }} Greater Chattanooga Darting Association
                </p>
                <nav aria-label="Footer navigation" class="flex gap-4 text-sm" style="color: #6b7280;">
                    <a href="{{ route('league-info') }}" class="hover:text-white">League Info</a>
                    <a href="{{ route('venues') }}" class="hover:text-white">Venues</a>
                    <a href="/admin" class="hover:text-white">Admin</a>
                </nav>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
