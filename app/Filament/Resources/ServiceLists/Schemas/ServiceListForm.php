<?php

namespace App\Filament\Resources\ServiceLists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceListForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Service List Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., Food, Drinks'),
                Select::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
