<?php

namespace App\Filament\Resources\Prices\Pages;

use App\Filament\Resources\Prices\PriceResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePrice extends CreateRecord
{
    protected static string $resource = PriceResource::class;

    protected function getFormActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $venueId = $data['venue_id'] ?? null;
        $categoryId = $data['category_id'] ?? null;
        
        unset($data['venue_id'], $data['category_id']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $venueId = $this->form->getState()['venue_id'] ?? null;
        $categoryId = $this->form->getState()['category_id'] ?? null;
        
        if ($venueId && $categoryId) {
            DB::table('venues_categories')->insert([
                'venue_id' => $venueId,
                'category_id' => $categoryId,
                'price_id' => $this->record->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

