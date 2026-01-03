<?php

namespace App\Filament\Resources\Grounds\Pages;

use App\Filament\Resources\Grounds\GroundResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateGround extends CreateRecord
{
    protected static string $resource = GroundResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->submit('create'),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
