<?php

namespace App\Filament\Resources\ServiceListDetails\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceListDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('serviceList.name')
                    ->label('Service List')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Service Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('retail')
                    ->label('Retail Price')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit_retail')
                    ->label('Retail Unit')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('wholesale')
                    ->label('Wholesale Price')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('unit_wholesale')
                    ->label('Wholesale Unit')
                    ->searchable()
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
