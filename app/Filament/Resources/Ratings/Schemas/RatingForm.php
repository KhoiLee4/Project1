<?php

namespace App\Filament\Resources\Ratings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RatingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('star_number')
                    ->label('Star Rating (1-5)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(5)
                    ->default(5),
                Textarea::make('review')
                    ->label('Review')
                    ->rows(5)
                    ->columnSpanFull(),
            ]);
    }
}
