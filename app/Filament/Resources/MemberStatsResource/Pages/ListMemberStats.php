<?php

namespace App\Filament\Resources\MemberStatsResource\Pages;

use App\Filament\Resources\MemberStatsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberStats extends ListRecords
{
    protected static string $resource = MemberStatsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
