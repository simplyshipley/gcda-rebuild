<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Season;
use Livewire\Component;
use Illuminate\Support\Collection;

class CurrentSeasonsWidget extends Component
{
    public function render()
    {
        $seasons = Season::with('league')
            ->where('status', 'current')
            ->orderBy('year', 'desc')
            ->get();

        return view('livewire.public.current-seasons-widget', [
            'seasons' => $seasons,
        ]);
    }
}
