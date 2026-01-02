<?php

namespace App\Filament\Resources\Venues\Pages;

use App\Filament\Resources\Venues\VenueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListVenues extends ListRecords
{
    protected static string $resource = VenueResource::class;

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
            $query->where('owner_id', $user->id);
        }
        
        return $query;
    }

    protected function getTableRecordUrl($record): ?string
    {
        return route('filament.admin.resources.grounds.index', [
            'venue_id' => $record->id,
        ]);
    }
}
