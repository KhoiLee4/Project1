<?php

namespace App\Filament\Resources\Grounds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GroundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ground Name')
                    ->required()
                    ->maxLength(100),
                Select::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(onBlur: false),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', modifyQueryUsing: function ($query, $get) {
                        $venueId = $get('venue_id');
                        if ($venueId) {
                            $venue = \App\Models\Venue::with('categories')->find($venueId);
                            if ($venue && $venue->categories->isNotEmpty()) {
                                $categoryIds = $venue->categories->pluck('id')->toArray();
                                $query->whereIn('id', $categoryIds);
                            } else {
                                $query->whereRaw('1 = 0');
                            }
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn ($get) => !empty($get('venue_id'))),
            ]);
    }
}
