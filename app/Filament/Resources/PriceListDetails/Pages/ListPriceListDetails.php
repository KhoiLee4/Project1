<?php

namespace App\Filament\Resources\PriceListDetails\Pages;

use App\Filament\Resources\PriceListDetails\PriceListDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPriceListDetails extends ListRecords
{
    protected static string $resource = PriceListDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
