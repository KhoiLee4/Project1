<?php

namespace App\Filament\Resources\Venues\Schemas;

use App\Models\Category;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VenueForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEdit = $schema->getLivewire() instanceof \Filament\Resources\Pages\EditRecord;
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Venue Name')
                    ->required()
                    ->maxLength(200),
                TextInput::make('sub_address')
                    ->label('Street Address')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $subAddress = $state ?? '';
                        $district = $get('district') ?? '';
                        $city = $get('city') ?? '';
                        $fullAddress = trim(implode(', ', array_filter([$subAddress, $district, $city])));
                        if ($fullAddress) {
                            $set('address', $fullAddress);
                        }
                    }),
                TextInput::make('district')
                    ->label('District')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $subAddress = $get('sub_address') ?? '';
                        $district = $state ?? '';
                        $city = $get('city') ?? '';
                        $fullAddress = trim(implode(', ', array_filter([$subAddress, $district, $city])));
                        if ($fullAddress) {
                            $set('address', $fullAddress);
                        }
                    }),
                TextInput::make('city')
                    ->label('City')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $subAddress = $get('sub_address') ?? '';
                        $district = $get('district') ?? '';
                        $city = $state ?? '';
                        $fullAddress = trim(implode(', ', array_filter([$subAddress, $district, $city])));
                        if ($fullAddress) {
                            $set('address', $fullAddress);
                        }
                    }),
                TextInput::make('address')
                    ->label('Full Address')
                    ->disabled()
                    ->dehydrated(true)
                    ->maxLength(300)
                    ->afterStateHydrated(function ($state, callable $set, $get) {
                        $subAddress = $get('sub_address') ?? '';
                        $district = $get('district') ?? '';
                        $city = $get('city') ?? '';
                        $fullAddress = trim(implode(', ', array_filter([$subAddress, $district, $city])));
                        if ($fullAddress) {
                            $set('address', $fullAddress);
                        }
                    }),
                TextInput::make('operating_time')
                    ->label('Operating Time')
                    ->placeholder('e.g., 6:00 – 22:00')
                    ->required()
                    ->maxLength(100),
                TextInput::make('phone_number1')
                    ->label('Phone Number 1')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                TextInput::make('phone_number2')
                    ->label('Phone Number 2')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->maxLength(255),
                TextInput::make('deposit')
                    ->label('Deposit (%)')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->step(0.01),
                Select::make('owner_id')
                    ->label('Owner')
                    ->relationship('owner', 'name', fn ($query) =>
                        $query->where('is_admin', 0)->where('role', 0)
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled($isEdit)
                    ->dehydrated(!$isEdit),
                Select::make('categories')
                    ->label('Categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
