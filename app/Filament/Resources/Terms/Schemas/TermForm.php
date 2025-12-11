<?php

namespace App\Filament\Resources\Terms\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TermForm
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
                TextInput::make('term_category')
                    ->label('Term Category')
                    ->numeric()
                    ->required(),
                DateTimePicker::make('update_time')
                    ->label('Update Time')
                    ->default(now())
                    ->required()
                    ->disabled()
                    ->displayFormat('d/m/Y H:i'),
                Textarea::make('content')
                    ->label('Term Content')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),
            ]);
    }
}
