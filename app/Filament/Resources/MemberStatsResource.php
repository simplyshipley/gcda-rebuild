<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberStatsResource\Pages;
use App\Filament\Resources\MemberStatsResource\RelationManagers;
use App\Models\MemberStats;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberStatsResource extends Resource
{
    protected static ?string $model = MemberStats::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'id')
                    ->required(),
                Forms\Components\Select::make('team_id')
                    ->relationship('team', 'name')
                    ->required(),
                Forms\Components\Select::make('season_id')
                    ->relationship('season', 'id')
                    ->required(),
                Forms\Components\Select::make('division_id')
                    ->relationship('division', 'id')
                    ->required(),
                Forms\Components\TextInput::make('mvp_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('fastest_501')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('stats_calculated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('season.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('division.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mvp_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fastest_501')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stats_calculated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberStats::route('/'),
            'create' => Pages\CreateMemberStats::route('/create'),
            'edit' => Pages\EditMemberStats::route('/{record}/edit'),
        ];
    }
}
