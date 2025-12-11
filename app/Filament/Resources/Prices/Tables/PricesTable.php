<?php

namespace App\Filament\Resources\Prices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Venue;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
class PricesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['venues', 'categories']);
            })
            ->columns([
                TextColumn::make('venue_name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->venues->first()?->name ?? '-'),
                TextColumn::make('category_name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->categories->first()?->name ?? '-'),
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
                SelectFilter::make('venue')
                    ->label('Venue')
                    ->options(Venue::pluck('name', 'id'))
                    ->query(function ($query, $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('venues', function ($q) use ($data) {
                                $q->where('venues.id', $data['value']);
                            });
                        }
                    })
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('category')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id'))
                    ->query(function ($query, $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('categories', function ($q) use ($data) {
                                $q->where('categories.id', $data['value']);
                            });
                        }
                    })
                    ->searchable()
                    ->preload(),
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

