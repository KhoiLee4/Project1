<?php

namespace App\Filament\Resources\PriceListDetails;

use App\Filament\Resources\PriceListDetails\Pages\CreatePriceListDetail;
use App\Filament\Resources\PriceListDetails\Pages\EditPriceListDetail;
use App\Filament\Resources\PriceListDetails\Pages\ListPriceListDetails;
use App\Filament\Resources\PriceListDetails\Schemas\PriceListDetailForm;
use App\Filament\Resources\PriceListDetails\Tables\PriceListDetailsTable;
use App\Models\PriceListDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PriceListDetailResource extends Resource
{
    protected static ?string $model = PriceListDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PriceListDetailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PriceListDetailsTable::configure($table);
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
            'index' => ListPriceListDetails::route('/'),
            'create' => CreatePriceListDetail::route('/create'),
            'edit' => EditPriceListDetail::route('/{record}/edit'),
        ];
    }
}
