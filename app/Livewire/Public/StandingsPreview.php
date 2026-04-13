<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Season;
use App\Models\Division;
use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Collection;

class StandingsPreview extends Component
{
    public function render()
    {
        // Grab first current season with its first division's top 5 teams
        $season = Season::where('status', 'current')
            ->orderBy('year', 'desc')
            ->first();

        $previews = collect();

        if ($season) {
            $divisions = Division::where('season_id', $season->id)
                ->orderBy('display_order')
                ->take(2)
                ->get();

            foreach ($divisions as $division) {
                $teams = Team::with('venue')
                    ->where('division_id', $division->id)
                    ->orderByDesc('starting_points')
                    ->take(5)
                    ->get();

                $previews->push([
                    'division' => $division,
                    'teams'    => $teams,
                ]);
            }
        }

        return view('livewire.public.standings-preview', [
            'season'   => $season,
            'previews' => $previews,
        ]);
    }
}
