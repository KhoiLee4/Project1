<?php

namespace App\Filament\Resources\ServiceLists\Pages;

use App\Filament\Resources\ServiceLists\ServiceListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceLists extends ListRecords
{
    protected static string $resource = ServiceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
