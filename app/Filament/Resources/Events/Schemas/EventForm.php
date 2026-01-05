<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('venue_id')
                    ->label('Venue')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
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
                DateTimePicker::make('start_date')
                    ->label('Start Date & Time')
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false),
                DateTimePicker::make('end_date')
                    ->label('End Date & Time')
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->after('start_date'),
            ]);
    }
}
