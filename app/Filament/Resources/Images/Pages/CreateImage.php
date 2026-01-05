<?php

namespace App\Filament\Resources\Images\Pages;

use App\Filament\Resources\Images\ImageResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateImage extends CreateRecord
{
    protected static string $resource = ImageResource::class;

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['image_url']) && is_array($data['image_url'])) {
            $filePath = $data['image_url'][0] ?? $data['image_url'];
            $data['image_url'] = Storage::disk('cloudinary')->url($filePath);
        } elseif (isset($data['image_url']) && is_string($data['image_url'])) {
            if (!str_starts_with($data['image_url'], 'http')) {
                $data['image_url'] = Storage::disk('cloudinary')->url($data['image_url']);
            }
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
