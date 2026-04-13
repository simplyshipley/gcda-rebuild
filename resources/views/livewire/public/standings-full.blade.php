<div>
    {{-- Season selector --}}
    @if($seasons->isNotEmpty())
        <div class="mb-6" role="tablist" aria-label="Select season">
            <div class="flex flex-wrap gap-2">
                @foreach($seasons as $season)
                    <button
                        role="tab"
                        aria-selected="{{ $selectedSeasonId === $season->id ? 'true' : 'false' }}"
                        wire:click="selectSeason({{ $season->id }})"
                        class="px-4 py-2 rounded text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="
                            background-color: {{ $selectedSeasonId === $season->id ? '#71a100' : '#1f2937' }};
                            color: {{ $selectedSeasonId === $season->id ? '#ffffff' : '#d1d5db' }};
                            border: 1px solid {{ $selectedSeasonId === $season->id ? '#71a100' : '#374151' }};
                            --tw-ring-color: #71a100;
                            --tw-ring-offset-color: #111827;
                        "
                    >
                        {{ $season->league->name ?? 'League' }}
                        {{ ucfirst($season->season_code) }} {{ $season->year }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Division navigation --}}
    @if($selectedSeasonId && $divisions->isNotEmpty())

        {{-- Mobile: select dropdown --}}
        <div class="md:hidden mb-4">
            <label for="division-select" class="sr-only">Select division</label>
            <select
                id="division-select"
                wire:change="selectDivision($event.target.value)"
                class="w-full rounded border px-3 py-2 text-sm"
                style="background-color: #1f2937; border-color: #374151; color: #f9fafb;"
            >
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ $selectedDivisionId === $division->id ? 'selected' : '' }}>
                        {{ $division->name ?? $division->code }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Desktop: tab strip --}}
        <div class="hidden md:flex gap-1 mb-4 border-b overflow-x-auto" style="border-color: #374151;" role="tablist" aria-label="Select division">
            @foreach($divisions as $division)
                <button
                    role="tab"
                    aria-selected="{{ $selectedDivisionId === $division->id ? 'true' : 'false' }}"
                    wire:click="selectDivision({{ $division->id }})"
                    class="px-4 py-2 text-sm font-medium whitespace-nowrap transition-colors border-b-2 -mb-px focus:outline-none focus:ring-2 focus:ring-inset"
                    style="
                        border-bottom-color: {{ $selectedDivisionId === $division->id ? '#71a100' : 'transparent' }};
                        color: {{ $selectedDivisionId === $division->id ? '#71a100' : '#9ca3af' }};
                        --tw-ring-color: #71a100;
                    "
                >
                    {{ $division->name ?? $division->code }}
                </button>
            @endforeach
        </div>

        {{-- Standings table --}}
        @if($teams->isNotEmpty())
            <div wire:loading.class="opacity-50 pointer-events-none" wire:target="selectDivision,selectSeason">
                <div class="overflow-x-auto rounded-lg border" style="border-color: #374151;">
                    <table class="min-w-full text-sm">
                        <caption class="sr-only">
                            Standing table for {{ $divisions->firstWhere('id', $selectedDivisionId)?->name ?? 'selected division' }}
                        </caption>
                        <thead>
                            <tr style="background-color: #1f2937; border-bottom: 1px solid #374151;">
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #9ca3af; width: 2.5rem;">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #9ca3af;">Team</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden sm:table-cell" style="color: #9ca3af;">Venue</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #9ca3af;">Pts</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide hidden md:table-cell" style="color: #9ca3af;">W</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide hidden md:table-cell" style="color: #9ca3af;">L</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teams as $index => $team)
                                <tr
                                    class="border-t transition-colors hover:bg-opacity-50"
                                    style="border-color: #374151; {{ $loop->even ? 'background-color: #161d2a;' : '' }}"
                                    onmouseenter="this.style.backgroundColor='#1e2a3a'"
                                    onmouseleave="this.style.backgroundColor='{{ $loop->even ? '#161d2a' : '' }}'"
                                >
                                    {{-- Rank --}}
                                    <td class="px-4 py-3 text-center font-mono text-xs" style="color: #6b7280;">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- Team name --}}
                                    <td class="px-4 py-3">
                                        <span class="standings-name-cell block font-semibold" style="color: #f9fafb;">
                                            {{ $team->name }}
                                        </span>
                                        @if($team->captain_name ?? null)
                                            <span class="text-xs" style="color: #6b7280;">{{ $team->captain_name }}</span>
                                        @endif
                                    </td>

                                    {{-- Venue --}}
                                    <td class="px-4 py-3 hidden sm:table-cell">
                                        <span class="standings-name-cell block text-xs" style="color: #9ca3af;">
                                            {{ $team->venue?->name ?? '—' }}
                                        </span>
                                    </td>

                                    {{-- Points --}}
                                    <td class="px-4 py-3 text-right font-mono font-bold" style="color: #71a100; font-size: 1rem;">
                                        {{ number_format($team->starting_points ?? 0, 1) }}
                                    </td>

                                    {{-- Wins --}}
                                    <td class="px-4 py-3 text-right font-mono hidden md:table-cell" style="color: #d1d5db;">
                                        {{ $team->wins ?? '—' }}
                                    </td>

                                    {{-- Losses --}}
                                    <td class="px-4 py-3 text-right font-mono hidden md:table-cell" style="color: #d1d5db;">
                                        {{ $team->losses ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Stale stats notice (G-076) --}}
                <p class="mt-3 text-xs text-right" style="color: #6b7280;">
                    Points updated after each match night. Stats reflect last recorded scoresheet.
                </p>
            </div>
        @else
            {{-- Empty state: division selected but no teams --}}
            <div class="rounded-lg border py-12 text-center" style="border-color: #374151; background-color: #1f2937;">
                <svg class="mx-auto h-10 w-10 mb-3" viewBox="0 0 36 36" fill="none" aria-hidden="true">
                    <circle cx="18" cy="18" r="17" stroke="#374151" stroke-width="2" fill="none"/>
                    <circle cx="18" cy="18" r="12" stroke="#374151" stroke-width="1.5" fill="none"/>
                    <circle cx="18" cy="18" r="7" stroke="#374151" stroke-width="1.5" fill="none"/>
                    <circle cx="18" cy="18" r="3" fill="#374151"/>
                </svg>
                <p class="text-sm font-medium" style="color: #d1d5db;">No teams in this division yet</p>
                <p class="text-xs mt-1" style="color: #6b7280;">Teams will appear once registered for this season</p>
            </div>
        @endif

    @elseif($selectedSeasonId && $divisions->isEmpty())
        {{-- Season selected but no divisions --}}
        <div class="rounded-lg border py-12 text-center" style="border-color: #374151; background-color: #1f2937;">
            <p class="text-sm font-medium" style="color: #d1d5db;">No divisions configured for this season</p>
        </div>

    @elseif($seasons->isEmpty())
        {{-- No current seasons at all --}}
        <div class="rounded-lg border py-12 text-center" style="border-color: #374151; background-color: #1f2937;">
            <p class="text-sm font-medium" style="color: #d1d5db;">No active seasons found</p>
            <p class="text-xs mt-1" style="color: #6b7280;">Check back when the next season begins</p>
        </div>
    @endif
</div>
