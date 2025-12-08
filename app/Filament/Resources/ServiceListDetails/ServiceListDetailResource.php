<?php

namespace App\Filament\Resources\ServiceListDetails;

use App\Filament\Resources\ServiceListDetails\Pages\CreateServiceListDetail;
use App\Filament\Resources\ServiceListDetails\Pages\EditServiceListDetail;
use App\Filament\Resources\ServiceListDetails\Pages\ListServiceListDetails;
use App\Filament\Resources\ServiceListDetails\Schemas\ServiceListDetailForm;
use App\Filament\Resources\ServiceListDetails\Tables\ServiceListDetailsTable;
use App\Models\ServiceListDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceListDetailResource extends Resource
{
    protected static ?string $model = ServiceListDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ServiceListDetailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceListDetailsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceListDetails::route('/'),
            'create' => CreateServiceListDetail::route('/create'),
            'edit' => EditServiceListDetail::route('/{record}/edit'),
        ];
    }
}
