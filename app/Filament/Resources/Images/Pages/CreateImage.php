<?php

namespace App\Filament\Resources\Images\Pages;

use App\Filament\Resources\Images\ImageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateImage extends CreateRecord
{
    protected static string $resource = ImageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['image_url']) && is_array($data['image_url'])) {
            $data['image_url'] = Storage::disk('public')->url($data['image_url'][0] ?? $data['image_url']);
        }
        return $data;
    }
}
