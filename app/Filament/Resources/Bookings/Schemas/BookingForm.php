<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;  
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEdit = $schema->getLivewire() instanceof \Filament\Resources\Pages\EditRecord;
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->preload()
                    ->required()
                    ->disabled($isEdit)
                    ->dehydrated(!$isEdit)
                    ->helperText(fn ($context) =>
                        $context === 'create'
                            ? 'Không có user? ➕ Nhấn "Create New User" để tạo nhanh.'
                            : null
                    )
                    ->createOptionForm([
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
                        ])
                        ->default('user')
                        ->disabled() 
                        ->required(),
                    Hidden::make('role')->default(1),
                    Hidden::make('is_admin')->default(0),
                    Toggle::make('is_active')
                        ->label('Is Active')
                        ->default(true)
                        ->required(),
                    Select::make('avatar_id')
                        ->label('Avatar')
                        ->relationship('avatar', 'name')
                        ->preload(),
                    Select::make('cover_image_id')
                        ->label('Cover Image')
                        ->relationship('coverImage', 'name')
                        ->preload(),
                    ])  
                    ->createOptionUsing(function (array $data) {
                        return \App\Models\User::create($data)->id;
                    }),
                DatePicker::make('date')
                    ->label('Date')
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
                    ->preload()
                    ->visible(fn ($get) => $get('is_event')),
            ]);
    }
}
