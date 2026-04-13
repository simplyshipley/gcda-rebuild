<?php

namespace App\Filament\Resources\LeagueMatchResource\Pages;

use App\Filament\Resources\LeagueMatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeagueMatches extends ListRecords
{
    protected static string $resource = LeagueMatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
