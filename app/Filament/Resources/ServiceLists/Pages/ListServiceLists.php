<?php

namespace App\Filament\Resources\ServiceLists\Pages;

use App\Filament\Resources\ServiceLists\ServiceListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListServiceLists extends ListRecords
{
    protected static string $resource = ServiceListResource::class;

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
            $query->whereIn('venue_id', $venueIds);
        }

        return $query;
    }
}
