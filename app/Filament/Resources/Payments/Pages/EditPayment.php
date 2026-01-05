<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canDelete = false;
        
        if ($user) {
            if ($user->is_admin == 1) {
                $canDelete = true;
            } elseif ($user->is_admin == 0 && $user->role == 0) {
                $this->record->load('booking.ground.venue');
                if ($this->record->booking && $this->record->booking->ground && $this->record->booking->ground->venue && $this->record->booking->ground->venue->owner_id == $user->id) {
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
            $this->record->load('booking.ground.venue');
            if (!$this->record->booking || !$this->record->booking->ground || !$this->record->booking->ground->venue || $this->record->booking->ground->venue->owner_id != $user->id) {
                abort(403, 'Bạn không có quyền chỉnh sửa payment này.');
            }
        }
    }

    protected function afterSave(): void
    {
        if ($this->record->booking_id) {
            $booking = \App\Models\Booking::find($this->record->booking_id);
            if ($booking) {
                $paymentStatus = $this->record->status;
                if ($paymentStatus === 'Paid') {
                    $booking->update(['status' => 'Completed']);
                } elseif ($paymentStatus === 'Cancelled' || $paymentStatus === 'Refunded') {
                    // Payment cancelled - booking vẫn giữ status hiện tại
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
