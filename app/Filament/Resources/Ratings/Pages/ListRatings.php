<?php

namespace App\Filament\Resources\Ratings\Pages;

use App\Filament\Resources\Ratings\RatingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRatings extends ListRecords
{
    protected static string $resource = RatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery()->orderBy('created_at', 'desc');
        $user = auth()->user();

        if ($user && $user->is_admin == 0 && $user->role == 0) {
            $venueIds = \App\Models\Venue::where('owner_id', $user->id)->pluck('id')->toArray();
            $query->whereIn('venue_id', $venueIds);
        }

        return $query;
    }
}
