<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    public function mount(): void
    {
        parent::mount();
        
        $bookingId = request()->query('booking_id') ?? request()->get('booking_id');
        if ($bookingId) {
            $booking = \App\Models\Booking::with(['ground.venue', 'ground.category', 'user'])->find($bookingId);
            
            if ($booking) {
                // Set booking_id in form data - this will be picked up by default() and getSelectedRecordUsing()
                $this->form->fill([
                    'booking_id' => $bookingId,
                ]);
                
                // Calculate amounts
                $this->calculateAndSetAmounts($bookingId);
            }
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $bookingId = request()->query('booking_id') ?? request()->get('booking_id');
        if ($bookingId && empty($data['booking_id'])) {
            $data['booking_id'] = $bookingId;
        }
        return $data;
    }

    protected function calculateAndSetAmounts($bookingId): void
    {
        $result = $this->calculatePaymentFromBooking($bookingId);
        $this->form->fill([
            'unit_price' => $result['unit_price'],
            'amount' => $result['amount'],
        ]);
    }

    protected function calculatePaymentFromBooking($bookingId): array
    {
        $booking = \App\Models\Booking::with(['ground.venue', 'ground.category'])->find($bookingId);
        
        if (!$booking || !$booking->ground || !$booking->ground->venue || !$booking->ground->category) {
            return ['unit_price' => 0, 'amount' => 0];
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

            $amount = round($unitPrice * $hours, 2);
            
            return [
                'unit_price' => $unitPrice,
                'amount' => $amount,
            ];
        }
        
        return ['unit_price' => 0, 'amount' => 0];
    }

    protected function getFormActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $bookingId = $data['booking_id'] ?? null;
        
        if ($bookingId) {
            $result = $this->calculatePaymentFromBooking($bookingId);
            $data['unit_price'] = $result['unit_price'];
            $data['amount'] = $result['amount'];
        }
        
        return $data;
    }
}
