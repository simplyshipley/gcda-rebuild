<div>
    @if($previews->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-2">
            @foreach($previews as $preview)
                <div class="rounded-lg border overflow-hidden" style="border-color: #374151;">
                    <div class="px-4 py-3 flex items-center justify-between" style="background-color: #1f2937;">
                        <h3 class="text-sm font-bold" style="color: #71a100;">
                            {{ $preview['division']->name ?? $preview['division']->code }}
                        </h3>
                        <span class="text-xs" style="color: #6b7280;">Top {{ $preview['teams']->count() }}</span>
                    </div>
                    <table class="w-full text-sm">
                        <caption class="sr-only">Top teams in {{ $preview['division']->name ?? $preview['division']->code }}</caption>
                        <thead>
                            <tr style="border-bottom: 1px solid #374151;">
                                <th scope="col" class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6b7280;">#</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide" style="color: #6b7280;">Team</th>
                                <th scope="col" class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wide" style="color: #6b7280;">Pts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preview['teams'] as $i => $team)
                                <tr class="{{ $loop->even ? '' : '' }} border-t" style="border-color: #1e2736; {{ $loop->even ? 'background-color: #161d2a;' : '' }}">
                                    <td class="px-4 py-2 text-xs font-mono" style="color: #4b5563;">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2 font-medium standings-name-cell" style="color: #f9fafb;">{{ $team->name }}</td>
                                    <td class="px-4 py-2 text-right font-mono font-bold" style="color: #71a100;">{{ number_format($team->starting_points ?? 0, 1) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-sm" style="color: #6b7280;">No standings data available yet.</p>
    @endif
</div>
