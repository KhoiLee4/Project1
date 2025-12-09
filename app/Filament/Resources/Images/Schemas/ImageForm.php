<?php

namespace App\Filament\Resources\Images\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class ImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Image Name')
                    ->maxLength(100),
                FileUpload::make('image_url')
                    ->label('Image')
                    ->image()
                    ->directory('images')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(10240) // 10MB
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'])
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->required()
                    ->helperText('Upload image file (max 10MB)')
                    ->getUploadedFileUsing(function ($file) {
                        return Storage::disk('public')->url($file);
                    })
                    ->dehydrated(true),
            ]);
    }
}
