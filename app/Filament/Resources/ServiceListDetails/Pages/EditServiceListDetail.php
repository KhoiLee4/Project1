<?php

namespace App\Filament\Resources\ServiceListDetails\Pages;

use App\Filament\Resources\ServiceListDetails\ServiceListDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceListDetail extends EditRecord
{
    protected static string $resource = ServiceListDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
