<?php

namespace App\Filament\Resources\Images\Pages;

use App\Filament\Resources\Images\ImageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditImage extends EditRecord
{
    protected static string $resource = ImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['image_url']) && is_array($data['image_url'])) {
            $filePath = $data['image_url'][0] ?? $data['image_url'];
            if ($filePath) {
                $data['image_url'] = Storage::disk('cloudinary')->url($filePath);
            }
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