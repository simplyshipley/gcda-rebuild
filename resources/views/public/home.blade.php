<x-layouts.public title="Home">

    {{-- Hero --}}
    <section class="border-b py-12" style="background: linear-gradient(135deg, #0f1e00 0%, #111827 60%); border-color: #374151;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-start justify-between gap-8">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl" style="color: #71a100;">
                        Greater Chattanooga Darting Association
                    </h1>
                    <p class="mt-3 text-lg" style="color: #9ca3af;">
                        Tuesday · Wednesday · Thursday leagues · Three seasons per year
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('standings') }}"
                           class="inline-flex items-center gap-2 rounded px-4 py-2 text-sm font-semibold shadow transition-colors"
                           style="background-color: #71a100; color: white;">
                            View Standings
                        </a>
                        <a href="{{ route('schedule') }}"
                           class="inline-flex items-center gap-2 rounded px-4 py-2 text-sm font-semibold border transition-colors hover:text-white"
                           style="border-color: #374151; color: #d1d5db; background-color: #1f2937;">
                            This Week's Schedule
                        </a>
                    </div>
                </div>

                {{-- Current week at a glance --}}
                <div class="rounded-lg p-4 w-full lg:w-72 shrink-0 border" style="background-color: #1f2937; border-color: #374151;">
                    <h2 class="text-sm font-semibold uppercase tracking-wide mb-3" style="color: #71a100;">Current Seasons</h2>
                    @livewire('public.current-seasons-widget')
                </div>
            </div>
        </div>
    </section>

    {{-- Live Standings --}}
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold" style="color: #f9fafb;">Current Standings</h2>
                <a href="{{ route('standings') }}" class="text-sm font-medium" style="color: #71a100;">
                    Full standings →
                </a>
            </div>

            @livewire('public.standings-preview')
        </div>
    </section>

    {{-- Big Shots highlight --}}
    <section class="py-10 border-t" style="border-color: #374151;">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold" style="color: #f9fafb;">Recent Big Shots</h2>
                <a href="{{ route('big-shots') }}" class="text-sm font-medium" style="color: #71a100;">
                    All patches →
                </a>
            </div>

            @livewire('public.recent-patches')
        </div>
    </section>

</x-layouts.public>
