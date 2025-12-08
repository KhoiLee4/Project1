<?php

namespace App\Filament\Resources\PriceLists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PriceListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Price List Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., Football, Badminton'),
                Select::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
