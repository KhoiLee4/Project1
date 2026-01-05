<?php

namespace App\Filament\Resources\Prices\Pages;

use App\Filament\Resources\Prices\PriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPrice extends EditRecord
{
    protected static string $resource = PriceResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $pivot = DB::table('venues_categories')
            ->where('price_id', $this->record->id)
            ->first();
        
        $canDelete = false;
        if ($user && $pivot) {
            if ($user->is_admin == 1) {
                $canDelete = true;
            } elseif ($user->is_admin == 0 && $user->role == 0) {
                $venue = \App\Models\Venue::find($pivot->venue_id);
                $canDelete = $venue && $venue->owner_id == $user->id;
            }
        }
        
        return [
            DeleteAction::make()
                ->visible($canDelete),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        $user = auth()->user();
        if ($user && $user->is_admin == 0 && $user->role == 0) {
            $pivot = DB::table('venues_categories')
                ->where('price_id', $this->record->id)
                ->first();
            
            if ($pivot) {
                $venue = \App\Models\Venue::find($pivot->venue_id);
                if (!$venue || $venue->owner_id != $user->id) {
                    abort(403, 'Bạn không có quyền chỉnh sửa price này.');
                }
            }
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $pivot = DB::table('venues_categories')
            ->where('price_id', $this->record->id)
            ->first();
        
        if ($pivot) {
            $data['venue_id'] = $pivot->venue_id;
            $data['category_id'] = $pivot->category_id;
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $venueId = $data['venue_id'] ?? null;
        $categoryId = $data['category_id'] ?? null;
        
        unset($data['venue_id'], $data['category_id']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        $formState = $this->form->getRawState();
        $venueId = $formState['venue_id'] ?? null;
        $categoryId = $formState['category_id'] ?? null;
        
        if ($venueId && $categoryId) {
            $oldPivot = DB::table('venues_categories')
                ->where('price_id', $this->record->id)
                ->first();
            
            if ($oldPivot) {
                if ($oldPivot->venue_id == $venueId && $oldPivot->category_id == $categoryId) {
                    DB::table('venues_categories')
                        ->where('venue_id', $venueId)
                        ->where('category_id', $categoryId)
                        ->update([
                            'price_id' => $this->record->id,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('venues_categories')
                        ->where('price_id', $this->record->id)
                        ->update(['price_id' => null]);
                    
                    $exists = DB::table('venues_categories')
                        ->where('venue_id', $venueId)
                        ->where('category_id', $categoryId)
                        ->exists();
                    
                    if ($exists) {
                        DB::table('venues_categories')
                            ->where('venue_id', $venueId)
                            ->where('category_id', $categoryId)
                            ->update([
                                'price_id' => $this->record->id,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('venues_categories')->insert([
                            'venue_id' => $venueId,
                            'category_id' => $categoryId,
                            'price_id' => $this->record->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                $exists = DB::table('venues_categories')
                    ->where('venue_id', $venueId)
                    ->where('category_id', $categoryId)
                    ->exists();
                
                if ($exists) {
                    DB::table('venues_categories')
                        ->where('venue_id', $venueId)
                        ->where('category_id', $categoryId)
                        ->update([
                            'price_id' => $this->record->id,
                            'updated_at' => now(),
                        ]);
                } else {
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
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

