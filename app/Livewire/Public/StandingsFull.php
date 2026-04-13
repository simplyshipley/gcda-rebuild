<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Season;
use App\Models\Division;
use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Collection;

class StandingsFull extends Component
{
    public ?int $selectedSeasonId = null;
    public ?int $selectedDivisionId = null;

    public function getDivisionsProperty(): Collection
    {
        if (! $this->selectedSeasonId) {
            return collect();
        }

        return Division::query()
            ->where('season_id', $this->selectedSeasonId)
            ->orderBy('display_order')
            ->get();
    }

    public function getTeamsProperty(): Collection
    {
        if (! $this->selectedDivisionId) {
            return collect();
        }

        return Team::query()
            ->with(['venue'])
            ->where('division_id', $this->selectedDivisionId)
            ->orderByDesc('starting_points')
            ->get();
    }

    public function selectSeason(int $seasonId): void
    {
        $this->selectedSeasonId = $seasonId;
        $this->selectedDivisionId = null;

        $firstDivision = Division::where('season_id', $seasonId)
            ->orderBy('display_order')
            ->first();

        if ($firstDivision) {
            $this->selectedDivisionId = $firstDivision->id;
        }
    }

    public function selectDivision(int $divisionId): void
    {
        $this->selectedDivisionId = $divisionId;
    }

    public function mount(): void
    {
        $firstSeason = Season::where('status', 'current')->first();
        if ($firstSeason) {
            $this->selectSeason($firstSeason->id);
        }
    }

    public function render()
    {
        $seasons = Season::with('league')
            ->where('status', 'current')
            ->orderBy('year', 'desc')
            ->get();

        return view('livewire.public.standings-full', [
            'seasons'   => $seasons,
            'divisions' => $this->divisions,
            'teams'     => $this->teams,
        ]);
    }
}
