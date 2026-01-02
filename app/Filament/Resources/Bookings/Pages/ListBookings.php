<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = auth()->user();

        if ($user && $user->is_admin == 0 && $user->role == 0) {
            $venueIds = \App\Models\Venue::where('owner_id', $user->id)->pluck('id')->toArray();
            $groundIds = \App\Models\Ground::whereIn('venue_id', $venueIds)->pluck('id')->toArray();
            $query->whereIn('ground_id', $groundIds);
        }

        return $query;
    }
}
