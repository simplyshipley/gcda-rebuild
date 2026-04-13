<div>
    @forelse($seasons as $season)
        <div class="{{ !$loop->first ? 'mt-3 pt-3 border-t' : '' }}" style="border-color: #374151;">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold" style="color: #f9fafb;">
                    {{ $season->league->name ?? 'League' }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded font-medium" style="background-color: #0d2a00; color: #71a100;">
                    Active
                </span>
            </div>
            <p class="text-xs mt-0.5" style="color: #9ca3af;">
                {{ ucfirst($season->season_code) }} {{ $season->year }}
                @if($season->current_week && $season->week_count)
                    &middot; Wk {{ $season->current_week }}/{{ $season->week_count }}
                @endif
            </p>
        </div>
    @empty
        <p class="text-sm" style="color: #6b7280;">No active seasons</p>
    @endforelse
</div>
