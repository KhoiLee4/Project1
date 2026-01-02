<?php

namespace App\Filament\Resources\Ratings\Pages;

use App\Filament\Resources\Ratings\RatingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRating extends EditRecord
{
    protected static string $resource = RatingResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canDelete = false;
        
        if ($user) {
            if ($user->is_admin == 1) {
                $canDelete = true;
            } elseif ($user->is_admin == 0 && $user->role == 0) {
                $this->record->load('venue');
                if ($this->record->venue && $this->record->venue->owner_id == $user->id) {
                    $canDelete = true;
                }
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
            $this->record->load('venue');
            if (!$this->record->venue || $this->record->venue->owner_id != $user->id) {
                abort(403, 'Bạn không có quyền chỉnh sửa rating này.');
            }
        }
    }
}
