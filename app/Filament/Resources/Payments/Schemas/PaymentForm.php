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
                    ->preload()
                    ->default(fn () => request()->get('booking_id'))
                    ->disabled()       // luôn disabled
                    ->dehydrated(true) // vẫn lưu vào DB
                    ->required()
                    ->afterStateHydrated(function ($state, callable $set, $get = null, $context = null) {
                        // Filament can call afterStateHydrated with different signatures
                        // Older versions: ($state, $set, $get)
                        // Newer versions: ($state, $set, $get, $context)
                        // Normalize: if $context is null but $get is a string like 'create', treat it as context
                        if ($context === null && is_string($get)) {
                            $context = $get;
                            $get = null;
                        }

                        // Only calculate when creating a new Payment and a booking id is present
                        if ($context === 'create' && $state) {
                            $booking = \App\Models\Booking::with(['ground.venue.prices', 'ground.category', 'user'])->find($state);

                            if ($booking && $booking->ground) {
                                $ground = $booking->ground;

                                // Lấy price từ venue và category của ground
                                // Tìm price có cả venue_id và category_id khớp trong pivot table
                                $price = $ground->venue->prices()
                                    ->wherePivot('category_id', $ground->category_id)
                                    ->wherePivot('venue_id', $ground->venue_id)
                                    ->first();

                                if ($price) {
                                    // Ưu tiên current_price, nếu không có thì dùng fixed_price
                                    $unitPrice = $price->current_price ?? $price->fixed_price ?? 0;
                                    $set('unit_price', $unitPrice);

                                    // Tính amount = unit_price * amount_time (số giờ)
                                    // amount_time có thể là số giờ (integer). Nếu null, try to compute from start/end_time
                                    if (!empty($booking->amount_time)) {
                                        $amount = $unitPrice * $booking->amount_time;
                                        $set('amount', $amount);
                                    } elseif (!empty($booking->start_time) && !empty($booking->end_time)) {
                                        try {
                                            $start = \Carbon\Carbon::parse($booking->start_time);
                                            $end = \Carbon\Carbon::parse($booking->end_time);
                                            $hours = $end->diffInMinutes($start) / 60;
                                            $amount = $unitPrice * $hours;
                                            $set('amount', $amount);
                                        } catch (\Exception $e) {
                                            // ignore parse errors and do not set amount
                                        }
                                    }
                                }
                            }
                        }
                    }),
                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true) // Quan trọng: để lưu vào DB
                    ->required()
                    ->prefix('$')
                    ->step(0.01),
                    
                TextInput::make('amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true) // Quan trọng: để lưu vào DB
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
