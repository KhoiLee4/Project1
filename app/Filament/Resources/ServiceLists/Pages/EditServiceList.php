<?php

namespace App\Filament\Resources\ServiceLists\Pages;

use App\Filament\Resources\ServiceLists\ServiceListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceList extends EditRecord
{
    protected static string $resource = ServiceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
