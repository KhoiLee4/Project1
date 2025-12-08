<?php

namespace App\Filament\Resources\ServiceListDetails\Pages;

use App\Filament\Resources\ServiceListDetails\ServiceListDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceListDetails extends ListRecords
{
    protected static string $resource = ServiceListDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
