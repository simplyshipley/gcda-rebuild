<?php

namespace App\Filament\Resources\MemberStatsResource\Pages;

use App\Filament\Resources\MemberStatsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberStats extends EditRecord
{
    protected static string $resource = MemberStatsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
