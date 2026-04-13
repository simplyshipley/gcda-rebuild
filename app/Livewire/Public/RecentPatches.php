<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Patch;
use Livewire\Component;
use Illuminate\Support\Collection;

class RecentPatches extends Component
{
    public function render()
    {
        $patches = Patch::with(['member', 'team', 'match'])
            ->orderByDesc('season_id')
            ->orderByDesc('id')
            ->take(12)
            ->get();

        return view('livewire.public.recent-patches', [
            'patches' => $patches,
        ]);
    }
}
