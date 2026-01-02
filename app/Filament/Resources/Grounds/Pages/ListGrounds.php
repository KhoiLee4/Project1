<?php

namespace App\Filament\Resources\Grounds\Pages;

use App\Filament\Resources\Grounds\GroundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
class ListGrounds extends ListRecords
{
    protected static string $resource = GroundResource::class;

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

        if ($venueId = request()->get('venue_id')) {
            $query->where('venue_id', $venueId);
        }

        if ($user && $user->is_admin == 0 && $user->role == 0) {
            $venueIds = \App\Models\Venue::where('owner_id', $user->id)->pluck('id')->toArray();
            $query->whereIn('venue_id', $venueIds);
        }

        return $query;
    }
}
