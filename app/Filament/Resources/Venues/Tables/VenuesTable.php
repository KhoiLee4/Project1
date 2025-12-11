<?php

namespace App\Filament\Resources\Venues\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VenuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => route('filament.admin.resources.grounds.index', [
                'venue_id' => $record->id
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Venue Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Address')
                    ->formatStateUsing(fn ($record) => $record->sub_address . ', ' . $record->district . ', ' . $record->city),
                TextColumn::make('phone_number1')
                    ->label('Phone Number'),
                TextColumn::make('operating_time')
                    ->label('Operating Time')
                    ->toggleable(),
                TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categories.name')
                    ->label('Categories')
                    ->badge()
                    ->separator(','),
                TextColumn::make('deposit')
                    ->label('Deposit (%)')
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
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('viewCalendar')
                    ->label('Xem Lịch Đặt Sân')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.pages.venue-booking-calendar', ['venue' => $record->id])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
