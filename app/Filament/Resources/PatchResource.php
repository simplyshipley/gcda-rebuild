<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatchResource\Pages;
use App\Filament\Resources\PatchResource\RelationManagers;
use App\Models\Patch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatchResource extends Resource
{
    protected static ?string $model = Patch::class;

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
                Forms\Components\Select::make('match_id')
                    ->relationship('match', 'id'),
                Forms\Components\TextInput::make('week_number')
                    ->numeric(),
                Forms\Components\TextInput::make('week_label')
                    ->maxLength(20),
                Forms\Components\TextInput::make('patch_type')
                    ->required(),
                Forms\Components\DatePicker::make('earned_at'),
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
                Tables\Columns\TextColumn::make('match.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('week_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('week_label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('patch_type'),
                Tables\Columns\TextColumn::make('earned_at')
                    ->date()
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
            'index' => Pages\ListPatches::route('/'),
            'create' => Pages\CreatePatch::route('/create'),
            'edit' => Pages\EditPatch::route('/{record}/edit'),
        ];
    }
}
