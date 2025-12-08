<?php

namespace App\Filament\Resources\ServiceLists;

use App\Filament\Resources\ServiceLists\Pages\CreateServiceList;
use App\Filament\Resources\ServiceLists\Pages\EditServiceList;
use App\Filament\Resources\ServiceLists\Pages\ListServiceLists;
use App\Filament\Resources\ServiceLists\Schemas\ServiceListForm;
use App\Filament\Resources\ServiceLists\Tables\ServiceListsTable;
use App\Models\ServiceList;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceListResource extends Resource
{
    protected static ?string $model = ServiceList::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ServiceListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceListsTable::configure($table);
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
            'index' => ListServiceLists::route('/'),
            'create' => CreateServiceList::route('/create'),
            'edit' => EditServiceList::route('/{record}/edit'),
        ];
    }
}
