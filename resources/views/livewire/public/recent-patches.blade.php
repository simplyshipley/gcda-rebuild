<div>
    @if($patches->isNotEmpty())
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($patches as $patch)
                <div class="rounded-lg border p-4 flex items-start gap-3" style="background-color: #1f2937; border-color: #374151;">
                    {{-- Patch icon: dartboard bull --}}
                    <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #0d2a00; border: 2px solid #71a100;">
                        <svg viewBox="0 0 20 20" fill="none" class="w-5 h-5" aria-hidden="true">
                            <circle cx="10" cy="10" r="9" stroke="#71a100" stroke-width="1.5"/>
                            <circle cx="10" cy="10" r="5" stroke="#71a100" stroke-width="1.5"/>
                            <circle cx="10" cy="10" r="2" fill="#71a100"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate" style="color: #f9fafb;">
                            {{ $patch->member?->fullName() ?? 'Unknown Player' }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: #71a100;">
                            {{ $patch->displayLabel() }}
                        </p>
                        <p class="text-xs mt-1 truncate" style="color: #6b7280;">
                            {{ $patch->team?->name ?? '—' }}
                            @if($patch->earned_at)
                                &middot; {{ $patch->earned_at->format('M j') }}
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-lg border py-10 text-center" style="border-color: #374151; background-color: #1f2937;">
            <p class="text-sm" style="color: #6b7280;">No big shots recorded yet this season</p>
        </div>
    @endif
</div>
