<?php

namespace App\Filament\Resources\ServiceListDetails\Pages;

use App\Filament\Resources\ServiceListDetails\ServiceListDetailResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceListDetail extends CreateRecord
{
    protected static string $resource = ServiceListDetailResource::class;

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
