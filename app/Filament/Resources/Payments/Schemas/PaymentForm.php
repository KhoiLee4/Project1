<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    private static function calculatePaymentAmounts($bookingId, callable $set): void
    {
        $booking = \App\Models\Booking::with(['ground.venue', 'ground.category', 'user'])->find($bookingId);

        if (!$booking || !$booking->ground || !$booking->ground->venue || !$booking->ground->category) {
            $set('unit_price', null);
            $set('amount', null);
            return;
        }

        $ground = $booking->ground;
        $venueId = $ground->venue_id;
        $categoryId = $ground->category_id;

        $bookingDate = \Carbon\Carbon::parse($booking->date);
        $dayOfWeek = $bookingDate->dayOfWeek;
        
        $dayGroup = 'Mon-Thu';
        if ($dayOfWeek >= 5) {
            $dayGroup = 'Fri-Sun';
        }

        $bookingStartTime = $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : null;

        $price = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
            $query->where('venues.id', $venueId)
                  ->where('venues_categories.category_id', $categoryId);
        })
        ->where('day', $dayGroup)
        ->when($bookingStartTime, function($query) use ($bookingStartTime) {
            $query->where('start_time', '<=', $bookingStartTime)
                  ->where('end_time', '>', $bookingStartTime);
        })
        ->first();

        if (!$price) {
            $price = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })
            ->where('day', $dayGroup)
            ->first();
        }

        if (!$price) {
            $price = \App\Models\Price::whereHas('venues', function($query) use ($venueId, $categoryId) {
                $query->where('venues.id', $venueId)
                      ->where('venues_categories.category_id', $categoryId);
            })->first();
        }

        if ($price) {
            $unitPrice = $price->current_price ?? $price->fixed_price ?? 0;
            $set('unit_price', $unitPrice);

            $hours = 0;
            if (!empty($booking->amount_time)) {
                $hours = $booking->amount_time;
            } elseif (!empty($booking->start_time) && !empty($booking->end_time)) {
                try {
                    $start = \Carbon\Carbon::parse($booking->start_time);
                    $end = \Carbon\Carbon::parse($booking->end_time);
                    $hours = $end->diffInMinutes($start) / 60;
                } catch (\Exception $e) {
                    $hours = 0;
                }
            }

            $amount = $unitPrice * $hours;
            $set('amount', round($amount, 2));
        } else {
            $set('unit_price', 0);
            $set('amount', 0);
        }
    }

    public static function configure(Schema $schema): Schema
    {
        $bookingId = request()->query('booking_id') ?? request()->get('booking_id');
        
        return $schema
            ->components([
                Select::make('booking_id')
                    ->label('Booking')
                    ->relationship('booking', 'id', modifyQueryUsing: function ($query) use ($bookingId) {
                        $query->with(['ground.venue', 'ground.category', 'user']);
                        // Ensure current booking is always included in preload results
                        // The query will return all bookings, but we ensure the current one is loaded
                        return $query;
                    })
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        if (!$record) {
                            return '';
                        }
                        
                        // Ensure relationships are loaded
                        if (!$record->relationLoaded('user')) {
                            $record->load('user');
                        }
                        if (!$record->relationLoaded('ground')) {
                            $record->load('ground');
                        }
                        
                        $date = $record->date instanceof \Carbon\Carbon 
                            ? $record->date->format('d/m/Y') 
                            : \Carbon\Carbon::parse($record->date)->format('d/m/Y');
                        
                        $startTime = $record->start_time instanceof \Carbon\Carbon 
                            ? $record->start_time->format('H:i') 
                            : \Carbon\Carbon::parse($record->start_time)->format('H:i');
                        
                        $endTime = $record->end_time instanceof \Carbon\Carbon 
                            ? $record->end_time->format('H:i') 
                            : \Carbon\Carbon::parse($record->end_time)->format('H:i');
                        
                        $userName = $record->user->name ?? 'N/A';
                        $groundName = $record->ground->name ?? 'N/A';
                        
                        return "{$date} {$startTime}-{$endTime} - {$userName} ({$groundName})";
                    })
                    ->getOptionLabelUsing(function ($value) {
                        // Fallback: load booking if not in query results
                        if ($value) {
                            $booking = \App\Models\Booking::with(['ground.venue', 'ground.category', 'user'])->find($value);
                            if ($booking) {
                                $date = $booking->date instanceof \Carbon\Carbon 
                                    ? $booking->date->format('d/m/Y') 
                                    : \Carbon\Carbon::parse($booking->date)->format('d/m/Y');
                                
                                $startTime = $booking->start_time instanceof \Carbon\Carbon 
                                    ? $booking->start_time->format('H:i') 
                                    : \Carbon\Carbon::parse($booking->start_time)->format('H:i');
                                
                                $endTime = $booking->end_time instanceof \Carbon\Carbon 
                                    ? $booking->end_time->format('H:i') 
                                    : \Carbon\Carbon::parse($booking->end_time)->format('H:i');
                                
                                $userName = $booking->user->name ?? 'N/A';
                                $groundName = $booking->ground->name ?? 'N/A';
                                
                                return "{$date} {$startTime}-{$endTime} - {$userName} ({$groundName})";
                            }
                        }
                        return $value ?? '';
                    })
                    ->getSelectedRecordUsing(function ($value) use ($bookingId) {
                        // Load booking when selected (especially for create form with query parameter)
                        $idToLoad = $value ?? $bookingId;
                        if ($idToLoad) {
                            $booking = \App\Models\Booking::with(['ground.venue', 'ground.category', 'user'])->find($idToLoad);
                            if ($booking) {
                                return $booking;
                            }
                        }
                        return null;
                    })
                    ->preload()
                    ->default(function ($livewire, $get) use ($bookingId) {
                        // Always return bookingId if available in create form
                        if ($bookingId && $livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                            return $bookingId;
                        }
                        // Fallback to current state if no query parameter
                        return $get('booking_id') ?? null;
                    })
                    ->disabled(function () use ($bookingId) {
                        return !empty($bookingId);
                    })
                    ->dehydrated(true)
                    ->afterStateHydrated(function ($state, callable $set, $get = null, $context = null) use ($bookingId) {
                        // Force load booking with relationships when state is hydrated
                        if ($state && ($context === 'create' || $context === null)) {
                            $bookingIdToUse = is_string($state) || is_numeric($state) ? $state : ($bookingId ?? null);
                            if ($bookingIdToUse) {
                                // Trigger calculation when booking is set from query parameter
                                self::calculatePaymentAmounts($bookingIdToUse, $set);
                            }
                        }
                    })
                    ->required()
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            self::calculatePaymentAmounts($state, $set);
                        } else {
                            $set('unit_price', 0);
                            $set('amount', 0);
                        }
                    })
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\Booking::query()
                            ->with(['ground.venue', 'ground.category', 'user'])
                            ->whereHas('user', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            })
                            ->orWhere('id', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($booking) {
                                $date = $booking->date instanceof \Carbon\Carbon 
                                    ? $booking->date->format('d/m/Y') 
                                    : \Carbon\Carbon::parse($booking->date)->format('d/m/Y');
                                
                                $startTime = $booking->start_time instanceof \Carbon\Carbon 
                                    ? $booking->start_time->format('H:i') 
                                    : \Carbon\Carbon::parse($booking->start_time)->format('H:i');
                                
                                $endTime = $booking->end_time instanceof \Carbon\Carbon 
                                    ? $booking->end_time->format('H:i') 
                                    : \Carbon\Carbon::parse($booking->end_time)->format('H:i');
                                
                                $userName = $booking->user->name ?? 'N/A';
                                $groundName = $booking->ground->name ?? 'N/A';
                                
                                $label = "{$date} {$startTime}-{$endTime} - {$userName} ({$groundName})";
                                
                                return [$booking->id => $label];
                            });
                    }),
                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->prefix('$')
                    ->step(0.01)
                    ->helperText('Giá mỗi giờ (tự động tính từ booking)')
                    ->default(0)
                    ->live(),
                    
                TextInput::make('amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->prefix('$')
                    ->step(0.01)
                    ->helperText('Tổng tiền = Unit Price × Số giờ (tự động tính)')
                    ->default(0)
                    ->live(),
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
