<?php

namespace App\Filament\Resources\PriceListDetails\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceListDetailsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('priceList.name')
                    ->label('Price List')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('day')
                    ->label('Day of Week')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('date')
                    ->label('Apply Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('End Time')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('current_price')
                    ->label('Current Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('fixed_price')
                    ->label('Fixed Price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
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
