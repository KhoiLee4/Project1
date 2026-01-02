<?php

namespace App\Filament\Resources\Terms\Pages;

use App\Filament\Resources\Terms\TermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTerms extends ListRecords
{
    protected static string $resource = TermResource::class;

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
