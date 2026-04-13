<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Members';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identity')
                    ->schema([
                        Forms\Components\TextInput::make('dart_card_number')
                            ->label('Dart Card Number')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(50)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(50)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('nickname')
                            ->maxLength(50)
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(150)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status Flags')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active player')
                            ->default(true),
                        Forms\Components\Toggle::make('is_substitute')
                            ->label('Substitute player'),
                        Forms\Components\Toggle::make('is_placeholder')
                            ->label('Placeholder entry'),
                        Forms\Components\TextInput::make('placeholder_type')
                            ->label('Placeholder type')
                            ->maxLength(50)
                            ->helperText('e.g. "open" or "bye" — only relevant when is_placeholder is on'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dart_card_number')
                    ->label('Card #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->getStateUsing(fn (Member $record): string => $record->fullName())
                    ->searchable(query: function ($query, string $search): void {
                        $query->where(function ($q) use ($search): void {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('nickname', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(['last_name', 'first_name']),
                Tables\Columns\BadgeColumn::make('is_substitute')
                    ->label('Sub')
                    ->getStateUsing(fn (Member $record): string => $record->is_substitute ? 'Sub' : 'Regular')
                    ->colors([
                        'warning' => 'Sub',
                        'success' => 'Regular',
                    ]),
                Tables\Columns\BadgeColumn::make('is_active')
                    ->label('Status')
                    ->getStateUsing(fn (Member $record): string => $record->is_active ? 'Active' : 'Inactive')
                    ->colors([
                        'success' => 'Active',
                        'danger'  => 'Inactive',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_name')
            ->filters([
                TernaryFilter::make('is_substitute')
                    ->label('Substitute players')
                    ->trueLabel('Subs only')
                    ->falseLabel('Regular only'),
                TernaryFilter::make('is_active')
                    ->label('Active status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->default(true),
                TernaryFilter::make('is_placeholder')
                    ->label('Placeholder entries')
                    ->trueLabel('Placeholders only')
                    ->falseLabel('Real players only'),
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
            'index'  => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit'   => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
