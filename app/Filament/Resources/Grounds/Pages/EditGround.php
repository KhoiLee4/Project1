<?php

namespace App\Filament\Resources\Grounds\Pages;

use App\Filament\Resources\Grounds\GroundResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGround extends EditRecord
{
    protected static string $resource = GroundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
