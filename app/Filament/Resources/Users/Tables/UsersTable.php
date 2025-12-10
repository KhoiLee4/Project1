<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->label('Phone Number'),
                BadgeColumn::make('role_display')
                    ->label('Role')
                    ->getStateUsing(function ($record) {
                        if ($record->is_admin == 1) {
                            return 'Admin';
                        }                   
                        if ($record->role == 1) {
                            return 'User';
                        }
                        return 'Owner';
                    })
                    ->colors([
                        'primary' => 'User',
                        'danger' => 'Admin',
                        'warning' => 'Owner',
                    ])
                    ->icons([
                        'heroicon-o-user' => 'User',
                        'heroicon-o-shield-check' => 'Admin',
                        'heroicon-o-home' => 'Owner',
                    ]),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('birthday')
                    ->label('Birthday')
                    ->date('d/m/Y')
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
                    ->label('Loại người dùng')
                    ->options([
                        'admin'    => 'Admin',
                        'owner'    => 'Owner',
                        'customer' => 'Customer',
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;

                        if (! $value) {
                            return $query;
                        }

                        return match ($value) {
                            'admin'    => $query->where('is_admin', 1),
                            'customer' => $query->where('is_admin', 0)->where('role', 1),
                            'owner'    => $query->where('is_admin', 0)->where('role', 0),
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
