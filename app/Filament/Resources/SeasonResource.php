<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonResource\Pages;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Seasons';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Season Identity')
                    ->schema([
                        Forms\Components\Select::make('league_id')
                            ->relationship('league', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2099)
                            ->columnSpan(1),
                        Forms\Components\Select::make('season_code')
                            ->label('Season')
                            ->required()
                            ->options([
                                'sum' => 'Summer',
                                'fal' => 'Fall',
                                'win' => 'Winter',
                            ])
                            ->columnSpan(1),
                        Forms\Components\Select::make('scoresheet_type')
                            ->label('Scoresheet type')
                            ->required()
                            ->options([
                                'sixman'  => '6-Man',
                                'trips'   => 'Trips',
                                'doubles' => 'Doubles',
                                'singles' => 'Singles',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\TextInput::make('week_count')
                            ->label('Total weeks')
                            ->required()
                            ->numeric()
                            ->default(14)
                            ->minValue(1)
                            ->maxValue(52)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('current_week')
                            ->label('Current week')
                            ->numeric()
                            ->minValue(0)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'future'    => 'Future',
                                'current'   => 'Current',
                                'completed' => 'Completed',
                            ])
                            ->default('future'),
                        Forms\Components\Placeholder::make('division_note')
                            ->label('Division codes')
                            ->content('Division codes must match exactly across teams and matches.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('league.name')
                    ->label('League')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('season_code')
                    ->label('Season')
                    ->getStateUsing(fn (Season $record): string => $record->label())
                    ->sortable(['year', 'season_code']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'future',
                        'success' => 'current',
                        'info'    => 'completed',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('current_week')
                    ->label('Week')
                    ->getStateUsing(fn (Season $record): string => $record->current_week
                        ? "Wk {$record->current_week} / {$record->week_count}"
                        : "— / {$record->week_count}")
                    ->sortable(['current_week']),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'future'    => 'Future',
                        'current'   => 'Current',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\SelectFilter::make('league')
                    ->relationship('league', 'name'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit'   => Pages\EditSeason::route('/{record}/edit'),
        ];
    }
}
