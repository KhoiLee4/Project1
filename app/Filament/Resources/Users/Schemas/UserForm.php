<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;

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
                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        0 => 'Male',
                        1 => 'Female',
                    ])
                    ->required()
                    ->default(1)
                    ->dehydrateStateUsing(fn($state) => is_null($state) ? null : (int) $state)
                    ->reactive(),
                DatePicker::make('birthday')
                    ->label('Birthday')
                    ->default(now())
                    ->required()
                    ->displayFormat('d/m/Y')
                    ->maxDate(now())               // không cho chọn ngày tương lai
                    ->rule('before_or_equal:today'), 
                Select::make('role_type')
                    ->label('Role')
                    ->options([
                        'user' => 'User',
                        'admin' => 'Admin',
                        'owner' => 'Owner',
                    ])
                    ->required()
                    ->native(false)
                    ->default('user')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === 'user') {
                            $set('role', 1);
                            $set('is_admin', 0);
                        }

                        if ($state === 'admin') {
                            $set('role', 0);
                            $set('is_admin', 1);
                        }

                        if ($state === 'owner') {
                            $set('role', 0);
                            $set('is_admin', 0);
                        }
                    }),
                Hidden::make('role')->default(1),
                Hidden::make('is_admin')->default(0),
                Toggle::make('is_active')
                    ->label('Is Active')
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
