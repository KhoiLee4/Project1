<?php

namespace App\Filament\Resources\Terms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('venue.name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('term_category')
                    ->label('Category')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('update_time')
                    ->label('Update Time')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Content')
                    ->limit(100)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
