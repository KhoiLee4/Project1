<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof \App\Filament\Resources\Users\Pages\CreateUser)
                    ->dehydrated(fn ($state) => filled($state))
                    ->minLength(8),
                Toggle::make('gender')
                    ->label('Gender (1=Female, 0=Male)')
                    ->default(true)
                    ->required(),
                DatePicker::make('birthday')
                    ->label('Birthday')
                    ->default(now())
                    ->required()
                    ->displayFormat('d/m/Y'),
                Toggle::make('role')
                    ->label('Role (1=User)')
                    ->default(true)
                    ->required(),
                Toggle::make('is_admin')
                    ->label('Is Admin (1=Admin, 0=Owner)')
                    ->default(false)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Is Active (1=Active)')
                    ->default(true)
                    ->required(),
                Select::make('avatar_id')
                    ->label('Avatar')
                    ->relationship('avatar', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('cover_image_id')
                    ->label('Cover Image')
                    ->relationship('coverImage', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
