<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : '-'),
                TextColumn::make('start_time')
                    ->label('Start Time')
                    ->sortable()
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('H:i') : '-'),
                TextColumn::make('end_time')
                    ->label('End Time')
                    ->sortable()
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('H:i') : '-'),
                TextColumn::make('ground.name')
                    ->label('Ground')
                    ->sortable()
                    ->placeholder('-')
                    ->default('-'),
                TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('event.start_date')
                    ->label('Event Start')
                    ->sortable()
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event.end_date')
                    ->label('Event End')
                    ->sortable()
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y H:i') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event.price')
                    ->label('Event Price')
                    ->money('VND')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('event.ticket_number')
                    ->label('Tickets')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Confirmed' => 'success',
                        'Cancelled' => 'danger',
                        'Completed' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('is_event')
                    ->label('Event')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_type')
                    ->label('Trạng thái')
                    ->options([
                        'pending'    => 'Pending',
                        'confirmed'    => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;

                        if (! $value) {
                            return $query;
                        }

                        return match ($value) {
                            'pending'   => $query->where('status', 'pending'),
                            'confirmed' => $query->where('status', 'confirmed'),
                            'completed' => $query->where('status', 'completed'),
                            'cancelled' => $query->where('status', 'cancelled'),
                        };
                    }),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'Confirmed']);
                        // Tạo payment pending khi confirm
                        $payment = \App\Models\Payment::firstOrCreate(
                            ['booking_id' => $record->id],
                            [
                                'amount' => 0,
                                'unit_price' => 0,
                                'method' => 'Cash',
                                'status' => 'Pending',
                            ]
                        );
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Booking confirmed')
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'Pending'),
                Action::make('cancel')
                    ->label('Hủy')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'Cancelled']);
                        // Hủy payment nếu có
                        \App\Models\Payment::where('booking_id', $record->id)->update(['status' => 'Cancelled']);
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Booking cancelled')
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, ['Pending', 'Confirmed'])),
                Action::make('createPayment')
                    ->label('Thanh toán')
                    ->icon('heroicon-o-credit-card')
                    ->url(fn ($record) => \App\Filament\Resources\Payments\PaymentResource::getUrl('create') . '?booking_id=' . $record->id)
                    ->visible(fn ($record) => $record->status === 'Confirmed')
                    ->color('warning'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
