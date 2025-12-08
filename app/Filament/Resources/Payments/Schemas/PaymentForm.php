<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('booking_id')
                    ->label('Booking')
                    ->relationship('booking', 'id')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->step(0.01),
                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->step(0.01),
                Select::make('method')
                    ->label('Payment Method')
                    ->options([
                        'Cash' => 'Cash',
                        'Card' => 'Card',
                        'Online' => 'Online',
                    ])
                    ->required(),
                Textarea::make('note')
                    ->label('Note')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Paid' => 'Paid',
                        'Cancelled' => 'Cancelled',
                        'Refunded' => 'Refunded',
                    ])
                    ->required()
                    ->default('Pending'),
            ]);
    }
}
