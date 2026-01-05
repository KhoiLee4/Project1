<?php

namespace App\Filament\Resources\ServiceListDetails\Pages;

use App\Filament\Resources\ServiceListDetails\ServiceListDetailResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceListDetail extends EditRecord
{
    protected static string $resource = ServiceListDetailResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canDelete = false;
        
        if ($user) {
            if ($user->is_admin == 1) {
                $canDelete = true;
            } elseif ($user->is_admin == 0 && $user->role == 0) {
                $this->record->load('serviceList.venue');
                if ($this->record->serviceList && $this->record->serviceList->venue && $this->record->serviceList->venue->owner_id == $user->id) {
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
            $this->record->load('serviceList.venue');
            if (!$this->record->serviceList || !$this->record->serviceList->venue || $this->record->serviceList->venue->owner_id != $user->id) {
                abort(403, 'Bạn không có quyền chỉnh sửa service list detail này.');
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
