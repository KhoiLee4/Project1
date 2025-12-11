<?php

namespace App\Filament\Resources\Ratings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Category;
class RatingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->reorderable(false)
            ->columns([
                TextColumn::make('user_id')
                    ->label('User ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('venue.name')
                    ->label('Venue')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('star_number')
                    ->label('Star Rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        5 => 'success',
                        4 => 'info',
                        3 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('review')
                    ->label('Review')
                    ->limit(50)
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
                    SelectFilter::make('category')
                        ->label('Category')
                        ->options(Category::pluck('name', 'id'))
                        ->query(function ($query, $data) {
                            if (isset($data['value'])) {
                                $query->whereHas('venue.categories', function ($q) use ($data) {
                                    $q->where('categories.id', $data['value']);
                                });
                            }
                        })
                        ->searchable()
                        ->preload(),
                    
                    SelectFilter::make('star_number')
                        ->label('Star Rating')
                        ->options([
                            5 => '5 Stars',
                            4 => '4 Stars',
                            3 => '3 Stars',
                            2 => '2 Stars',
                            1 => '1 Star',
                        ]),
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
