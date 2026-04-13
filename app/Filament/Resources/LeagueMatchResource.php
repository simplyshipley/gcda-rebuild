<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeagueMatchResource\Pages;
use App\Models\Division;
use App\Models\LeagueMatch;
use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeagueMatchResource extends Resource
{
    protected static ?string $model = LeagueMatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Matches';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Match Context')
                    ->schema([
                        Forms\Components\Select::make('season_id')
                            ->relationship('season', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Season $record): string => $record->label())
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Forms\Components\Select::make('division_id')
                            ->relationship('division', 'code')
                            ->required()
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('week_number')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('match_date')
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_playoff')
                            ->label('Playoff match')
                            ->columnSpan(1),
                        Forms\Components\Select::make('playoff_round')
                            ->options([
                                'Q' => 'Quarterfinals',
                                'S' => 'Semifinals',
                                'F' => 'Finals',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Teams & Score')
                    ->schema([
                        Forms\Components\Select::make('home_team_id')
                            ->relationship('homeTeam', 'name')
                            ->required()
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\Select::make('away_team_id')
                            ->relationship('awayTeam', 'name')
                            ->required()
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('home_score')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('away_score')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Scoresheet Status')
                    ->schema([
                        Forms\Components\Select::make('received_status')
                            ->required()
                            ->options([
                                'pending'  => 'Pending',
                                'received' => 'Received',
                                'missing'  => 'Missing',
                                'bye'      => 'Bye',
                            ])
                            ->default('pending'),
                        Forms\Components\DateTimePicker::make('received_at')
                            ->label('Received at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season.id')
                    ->label('Season')
                    ->getStateUsing(fn (LeagueMatch $record): string => $record->season?->label() ?? '—')
                    ->sortable(['season_id']),
                Tables\Columns\TextColumn::make('division.code')
                    ->label('Div')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('week_number')
                    ->label('Week')
                    ->getStateUsing(fn (LeagueMatch $record): string => $record->weekLabel())
                    ->sortable(),
                Tables\Columns\TextColumn::make('matchup')
                    ->label('Matchup')
                    ->getStateUsing(fn (LeagueMatch $record): string =>
                        ($record->homeTeam?->name ?? '?') . ' vs ' . ($record->awayTeam?->name ?? '?'))
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('homeTeam', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('awayTeam', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->getStateUsing(fn (LeagueMatch $record): string =>
                        $record->home_score !== null
                            ? "{$record->home_score} – {$record->away_score}"
                            : '—'),
                Tables\Columns\BadgeColumn::make('received_status')
                    ->label('Scoresheet')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'received',
                        'danger'  => 'missing',
                        'gray'    => 'bye',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('match_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('week_number', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('season_id')
                    ->label('Season')
                    ->options(fn (): array => Season::orderByDesc('year')
                        ->get()
                        ->mapWithKeys(fn (Season $s): array => [$s->id => $s->label()])
                        ->all()),
                Tables\Filters\SelectFilter::make('division_id')
                    ->label('Division')
                    ->options(fn (): array => Division::orderBy('code')
                        ->pluck('code', 'id')
                        ->all()),
                Tables\Filters\SelectFilter::make('received_status')
                    ->label('Scoresheet status')
                    ->options([
                        'pending'  => 'Pending',
                        'received' => 'Received',
                        'missing'  => 'Missing',
                        'bye'      => 'Bye',
                    ]),
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
            'index'  => Pages\ListLeagueMatches::route('/'),
            'create' => Pages\CreateLeagueMatch::route('/create'),
            'edit'   => Pages\EditLeagueMatch::route('/{record}/edit'),
        ];
    }
}
