<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canDelete = false;
        
        if ($user) {
            if ($user->is_admin == 1) {
                $canDelete = true;
            } elseif ($user->is_admin == 0 && $user->role == 0) {
                $this->record->load('ground.venue');
                if ($this->record->ground && $this->record->ground->venue && $this->record->ground->venue->owner_id == $user->id) {
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
        
        $this->record->load(['user', 'ground.venue']);
        
        $user = auth()->user();
        if ($user && $user->is_admin == 0 && $user->role == 0) {
            if (!$this->record->ground || !$this->record->ground->venue || $this->record->ground->venue->owner_id != $user->id) {
                abort(403, 'Bạn không có quyền chỉnh sửa booking này.');
            }
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Disable all fields if booking is cancelled or completed
        if (in_array($this->record->status, ['Cancelled', 'Completed'])) {
            $this->form->disabled();
        }
        
        if ($this->record->ground && $this->record->ground->venue) {
            $data['venue_id'] = $this->record->ground->venue->id;
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['venue_id']);
        return $data;
    }
}
