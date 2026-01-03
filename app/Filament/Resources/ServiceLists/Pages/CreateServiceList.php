<?php

namespace App\Filament\Resources\ServiceLists\Pages;

use App\Filament\Resources\ServiceLists\ServiceListResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceList extends CreateRecord
{
    protected static string $resource = ServiceListResource::class;

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
