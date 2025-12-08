<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Event Name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('price')
                    ->label('Ticket Price')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->step(0.01),
                TextInput::make('ticket_number')
                    ->label('Ticket Number')
                    ->numeric()
                    ->required()
                    ->default(1)
                    ->minValue(1),
                Textarea::make('level')
                    ->label('Level')
                    ->rows(2)
                    ->maxLength(255),
            ]);
    }
}
