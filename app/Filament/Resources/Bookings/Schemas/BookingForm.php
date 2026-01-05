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
        $currentUserId = $isEdit ? $schema->getLivewire()->record->user_id : null;
        $isReadOnly = $isEdit && in_array($schema->getLivewire()->record->status ?? '', ['Cancelled', 'Completed']);
        
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name', modifyQueryUsing: function ($query) use ($currentUserId) {
                        $query->where(function($q) use ($currentUserId) {
                            $q->where('is_admin', 0)->where('role', 1);
                            if ($currentUserId) {
                                $q->orWhere('id', $currentUserId);
                            }
                        });
                        return $query;
                    })
                    ->preload()
                    ->required()
                    ->disabled($isEdit)
                    ->dehydrated(true)
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
                        ->live(),
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
                Toggle::make('is_event')
                    ->label('Is Event (1=Event, 0=Normal)')
                    ->default(false)
                    ->required()
                    ->live()
                    ->disabled($isReadOnly),
                DatePicker::make('date')
                    ->label('Date')
                    ->required(fn ($get) => !$get('is_event'))
                    ->displayFormat('d/m/Y')
                    ->visible(fn ($get) => !$get('is_event'))
                    ->disabled($isReadOnly),
                TimePicker::make('start_time')
                    ->label('Start Time')
                    ->required(fn ($get) => !$get('is_event'))
                    ->seconds(false)
                    ->disabled($isReadOnly)
                    ->visible(fn ($get) => !$get('is_event'))
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $startTime = $state;
                        $endTime = $get('end_time');
                        if ($startTime && $endTime && !$get('is_event')) {
                            try {
                                $start = \Carbon\Carbon::parse($startTime);
                                $end = \Carbon\Carbon::parse($endTime);
                                $hours = $end->diffInMinutes($start) / 60;
                                $set('amount_time', max(0, round($hours, 2)));
                            } catch (\Exception $e) {
                                $set('amount_time', 0);
                            }
                        }
                    }),
                TimePicker::make('end_time')
                    ->label('End Time')
                    ->required(fn ($get) => !$get('is_event'))
                    ->seconds(false)
                    ->disabled($isReadOnly)
                    ->visible(fn ($get) => !$get('is_event'))
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $startTime = $get('start_time');
                        $endTime = $state;
                        if ($startTime && $endTime && !$get('is_event')) {
                            try {
                                $start = \Carbon\Carbon::parse($startTime);
                                $end = \Carbon\Carbon::parse($endTime);
                                $hours = $end->diffInMinutes($start) / 60;
                                $set('amount_time', max(0, round($hours, 2)));
                            } catch (\Exception $e) {
                                $set('amount_time', 0);
                            }
                        }
                    }),
                TextInput::make('amount_time')
                    ->label('Total Hours')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->visible(fn ($get) => !$get('is_event'))
                    ->dehydrated(true),
                Select::make('venue_id')
                    ->label('Venue')
                    ->options(function () {
                        if (!\Illuminate\Support\Facades\Auth::check()) {
                            return [];
                        }
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if ($user && $user->is_admin == 1) {
                            return \App\Models\Venue::pluck('name', 'id')->toArray();
                        } elseif ($user && $user->is_admin == 0 && $user->role == 0) {
                            return \App\Models\Venue::where('owner_id', $user->id)->pluck('name', 'id')->toArray();
                        }
                        return [];
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled($isReadOnly)
                    ->live(onBlur: false)
                    ->dehydrated(false)
                    ->visible(function () {
                        if (!\Illuminate\Support\Facades\Auth::check()) {
                            return false;
                        }
                        $user = \Illuminate\Support\Facades\Auth::user();
                        return ($user->is_admin == 1) || ($user->is_admin == 0 && $user->role == 0);
                    }),
                Select::make('ground_id')
                    ->label('Ground')
                    ->options(function ($get) {
                        $venueId = $get('venue_id');
                        if (!\Illuminate\Support\Facades\Auth::check()) {
                            return [];
                        }
                        $user = \Illuminate\Support\Facades\Auth::user();
                        
                        if ($venueId) {
                            return \App\Models\Ground::where('venue_id', $venueId)->pluck('name', 'id')->toArray();
                        }
                        
                        if ($user && $user->is_admin == 1) {
                            return \App\Models\Ground::pluck('name', 'id')->toArray();
                        } elseif ($user && $user->is_admin == 0 && $user->role == 0) {
                            $venueIds = \App\Models\Venue::where('owner_id', $user->id)->pluck('id')->toArray();
                            return \App\Models\Ground::whereIn('venue_id', $venueIds)->pluck('name', 'id')->toArray();
                        }
                        
                        return \App\Models\Ground::pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required(fn ($get) => !$get('is_event'))
                    ->visible(fn ($get) => !$get('is_event'))
                    ->disabled($isReadOnly)
                    ->live(onBlur: false),
                Select::make('target')
                    ->label('Target Audience')
                    ->options([
                        'student' => 'Student',
                        'adult' => 'Người lớn',
                    ])
                    ->searchable()
                    ->disabled($isReadOnly),
                Textarea::make('customer_note')
                    ->label('Customer Note')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled($isReadOnly),
                Textarea::make('owner_note')
                    ->label('Owner Note')
                    ->rows(3)
                    ->columnSpanFull()
                    ->disabled($isReadOnly),
                TextInput::make('quantity')
                    ->label('Quantity (People/Tickets)')
                    ->numeric()
                    ->default(fn ($get) => $get('is_event') ? 1 : 30)
                    ->required()
                    ->disabled($isReadOnly),
                TextInput::make('total_price')
                    ->label('Total Price')
                    ->numeric()
                    ->default(0)
                    ->prefix('₫')
                    ->disabled($isReadOnly),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Confirmed' => 'Confirmed',
                        'Cancelled' => 'Cancelled',
                        'Completed' => 'Completed',
                    ])
                    ->required()
                    ->default('Pending')
                    ->disabled($isReadOnly),
                Select::make('event_id')
                    ->label('Event')
                    ->options(function ($get) {
                        $venueId = $get('venue_id');
                        if (!$venueId) {
                            // Try to get venue_id from ground if available
                            $groundId = $get('ground_id');
                            if ($groundId) {
                                $ground = \App\Models\Ground::find($groundId);
                                if ($ground) {
                                    $venueId = $ground->venue_id;
                                }
                            }
                        }
                        
                        if ($venueId) {
                            return \App\Models\Event::where('venue_id', $venueId)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        
                        return [];
                    })
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('is_event'))
                    ->required(fn ($get) => $get('is_event'))
                    ->disabled($isReadOnly)
                    ->live(),
            ]);
    }
}
