<?php

namespace App\Filament\Resources\Grounds\Pages;

use App\Filament\Resources\Grounds\GroundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGrounds extends ListRecords
{
    protected static string $resource = GroundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
