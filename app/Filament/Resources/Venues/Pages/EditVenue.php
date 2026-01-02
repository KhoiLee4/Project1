<?php

namespace App\Filament\Resources\Venues\Pages;

use App\Filament\Resources\Venues\VenueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVenue extends EditRecord
{
    protected static string $resource = VenueResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canDelete = $user && ($user->is_admin == 1 || ($user->is_admin == 0 && $user->role == 0 && $this->record->owner_id == $user->id));
        
        return [
            DeleteAction::make()
                ->visible($canDelete),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        $user = auth()->user();
        if ($user && $user->is_admin == 0 && $user->role == 0 && $this->record->owner_id != $user->id) {
            abort(403, 'Bạn không có quyền chỉnh sửa venue này.');
        }
    }
}
