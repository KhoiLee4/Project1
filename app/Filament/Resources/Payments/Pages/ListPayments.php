<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = auth()->user();

        if ($user && $user->is_admin == 0 && $user->role == 0) {
            $venueIds = \App\Models\Venue::where('owner_id', $user->id)->pluck('id')->toArray();
            $groundIds = \App\Models\Ground::whereIn('venue_id', $venueIds)->pluck('id')->toArray();
            $bookingIds = \App\Models\Booking::whereIn('ground_id', $groundIds)->pluck('id')->toArray();
            $query->whereIn('booking_id', $bookingIds);
        }

        return $query;
    }
}
