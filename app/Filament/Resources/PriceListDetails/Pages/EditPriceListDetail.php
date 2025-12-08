<?php

namespace App\Filament\Resources\PriceListDetails\Pages;

use App\Filament\Resources\PriceListDetails\PriceListDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPriceListDetail extends EditRecord
{
    protected static string $resource = PriceListDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
