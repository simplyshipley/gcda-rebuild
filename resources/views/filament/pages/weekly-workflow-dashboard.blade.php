<x-filament-panels::page>

    {{-- Current seasons grid --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Active Seasons</h2>

        @php $seasons = $this->getCurrentSeasons(); @endphp

        @if ($seasons->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No seasons currently marked as "current".</p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($seasons as $season)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ $season->league?->name ?? 'Unknown League' }}
                        </div>
                        <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $season->label() }}
                        </div>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            Week {{ $season->current_week ?? '—' }} of {{ $season->week_count }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mb-6">

        {{-- Pending scoresheets --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning-100 dark:bg-warning-400/10">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Pending scoresheets
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getPendingScoresheetsCount() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Patches this week --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success-100 dark:bg-success-400/10">
                    <x-heroicon-o-star class="h-5 w-5 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Patches (last 7 days)
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getPatchesThisWeek() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Last stats update --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-info-100 dark:bg-info-400/10">
                    <x-heroicon-o-arrow-path class="h-5 w-5 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        Last stats update
                    </div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $this->getLastStatsUpdate() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Quick action buttons --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <h2 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-3">
            <x-filament::button
                icon="heroicon-o-clipboard-document-check"
                href="{{ route('filament.admin.resources.league-matches.create') }}"
                tag="a"
                color="primary"
            >
                Enter Scoresheet
            </x-filament::button>

            <x-filament::button
                icon="heroicon-o-star"
                href="{{ route('filament.admin.resources.patches.create') }}"
                tag="a"
                color="warning"
            >
                Add Patches
            </x-filament::button>

            <x-filament::button
                icon="heroicon-o-arrow-path"
                href="{{ route('filament.admin.resources.member-stats.index') }}"
                tag="a"
                color="gray"
            >
                Update Stats
            </x-filament::button>
        </div>
    </div>

</x-filament-panels::page>
