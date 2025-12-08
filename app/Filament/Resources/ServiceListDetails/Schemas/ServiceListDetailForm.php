<?php

namespace App\Filament\Resources\ServiceListDetails\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceListDetailForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_list_id')
                    ->label('Service List')
                    ->relationship('serviceList', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Service Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., Sea cucumber, Revive water'),
                TextInput::make('wholesale')
                    ->label('Wholesale Price')
                    ->maxLength(50)
                    ->placeholder('e.g., 20,000'),
                TextInput::make('unit_wholesale')
                    ->label('Wholesale Unit')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g., 1 box'),
                TextInput::make('retail')
                    ->label('Retail Price')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g., 20,000'),
                TextInput::make('unit_retail')
                    ->label('Retail Unit')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('e.g., 1 bottle'),
            ]);
    }
}
