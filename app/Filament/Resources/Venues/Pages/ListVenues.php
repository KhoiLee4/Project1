<?php

namespace App\Filament\Resources\Venues\Pages;

use App\Filament\Resources\Venues\VenueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVenues extends ListRecords
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

        // \Filament\Actions\Action::make('notice')
        //     ->label('⚠️ Lưu ý: Tạo Owner trước khi tạo Venue')
        //     ->disabled()
        //     ->color('warning'),
        ];
    }
    protected function getTableRecordUrl($record): ?string
    {
        return route('filament.admin.resources.grounds.index', [
            'venue_id' => $record->id,
        ]);
    }
}
