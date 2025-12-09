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

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     if (isset($data['image_url']) && is_array($data['image_url'])) {
    //         $data['image_url'] = isset($data['image_url'][0]) ? Storage::disk('public')->url($data['image_url'][0]) : null;
    //     }
    //     return $data;
    // }
}