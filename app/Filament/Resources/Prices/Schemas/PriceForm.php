<?php

namespace App\Filament\Resources\Prices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class PriceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Apply Date')
                    ->displayFormat('d/m/Y')
                    ->helperText('e.g., 30/4 - 1/5'),
                TextInput::make('day')
                    ->label('Day of Week')
                    ->maxLength(20)
                    ->placeholder('e.g., Mon-Thu, Fri-Sun'),
                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->required()
                    ->seconds(false),
                TimePicker::make('end_time')
                    ->label('End Time')
                    ->required()
                    ->seconds(false),
                TextInput::make('fixed_price')
                    ->label('Fixed Price')
                    ->numeric()
                    ->prefix('$')
                    ->step(0.01)
                    ->helperText('For regular customers'),
                TextInput::make('current_price')
                    ->label('Current Price')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->step(0.01)
                    ->helperText('For one-time customers'),
            ]);
    }
}

