<?php

namespace App\Filament\Resources\Prices\Pages;

use App\Filament\Resources\Prices\PriceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePrice extends CreateRecord
{
    protected static string $resource = PriceResource::class;

    protected ?string $venueId = null;
    protected ?string $categoryId = null;

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
        $formState = $this->form->getRawState();
        $this->venueId = $formState['venue_id'] ?? null;
        $this->categoryId = $formState['category_id'] ?? null;
        
        unset($data['venue_id'], $data['category_id']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->venueId && $this->categoryId) {
            $exists = DB::table('venues_categories')
                ->where('venue_id', $this->venueId)
                ->where('category_id', $this->categoryId)
                ->exists();
            
            if ($exists) {
                DB::table('venues_categories')
                    ->where('venue_id', $this->venueId)
                    ->where('category_id', $this->categoryId)
                    ->update([
                        'price_id' => $this->record->id,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('venues_categories')->insert([
                    'venue_id' => $this->venueId,
                    'category_id' => $this->categoryId,
                    'price_id' => $this->record->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

