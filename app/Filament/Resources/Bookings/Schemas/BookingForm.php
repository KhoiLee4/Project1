<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BookingForm
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
                DatePicker::make('date')
                    ->label('Booking Date')
                    ->required()
                    ->displayFormat('d/m/Y'),
                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->required()
                    ->seconds(false),
                TimePicker::make('end_time')
                    ->label('End Time')
                    ->required()
                    ->seconds(false),
                TextInput::make('amount_time')
                    ->label('Total Hours')
                    ->numeric()
                    ->required()
                    ->default(1),
                Toggle::make('is_event')
                    ->label('Is Event (1=Event, 0=Normal)')
                    ->default(false)
                    ->required(),
                Select::make('ground_id')
                    ->label('Ground')
                    ->relationship('ground', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('target')
                    ->label('Target Audience')
                    ->maxLength(255)
                    ->placeholder('e.g., students'),
                Textarea::make('customer_note')
                    ->label('Customer Note')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('owner_note')
                    ->label('Owner Note')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->label('Quantity (People/Tickets)')
                    ->numeric()
                    ->default(30)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Confirmed' => 'Confirmed',
                        'Cancelled' => 'Cancelled',
                        'Completed' => 'Completed',
                    ])
                    ->required()
                    ->default('Pending'),
                Select::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('is_event')),
            ]);
    }
}
