<?php

namespace App\Filament\Pages;

use App\Models\LeagueMatch;
use App\Models\MemberStats;
use App\Models\Patch;
use App\Models\Season;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class WeeklyWorkflowDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Weekly Dashboard';

    protected static string $view = 'filament.pages.weekly-workflow-dashboard';

    protected static ?int $navigationSort = 0;

    /**
     * Make this the default landing page for admin.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    /**
     * Current seasons keyed by league name.
     *
     * @return Collection<int, Season>
     */
    public function getCurrentSeasons(): Collection
    {
        return Season::with('league')
            ->where('status', 'current')
            ->orderBy('league_id')
            ->get();
    }

    /**
     * Matches in the current week that have not yet been received.
     */
    public function getPendingScoresheetsCount(): int
    {
        return LeagueMatch::whereHas('season', fn ($q) => $q->where('status', 'current'))
            ->whereRaw('week_number = (
                SELECT current_week FROM seasons WHERE seasons.id = league_matches.season_id
            )')
            ->where('received_status', 'pending')
            ->count();
    }

    /**
     * Patches entered in the last 7 days (rolling week).
     */
    public function getPatchesThisWeek(): int
    {
        return Patch::where('earned_at', '>=', now()->subDays(7))->count();
    }

    /**
     * Most recent stats calculation across all seasons.
     */
    public function getLastStatsUpdate(): ?string
    {
        $latest = MemberStats::whereNotNull('stats_calculated_at')
            ->orderByDesc('stats_calculated_at')
            ->value('stats_calculated_at');

        return $latest ? \Carbon\Carbon::parse($latest)->diffForHumans() : 'Never';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enter_scoresheet')
                ->label('Enter Scoresheet')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->url(fn () => route('filament.admin.resources.league-matches.create')),

            Action::make('add_patches')
                ->label('Add Patches')
                ->icon('heroicon-o-star')
                ->color('warning')
                ->url(fn () => route('filament.admin.resources.patches.create')),

            Action::make('update_stats')
                ->label('Update Stats')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->url(fn () => route('filament.admin.resources.member-stats.index')),
        ];
    }
}
