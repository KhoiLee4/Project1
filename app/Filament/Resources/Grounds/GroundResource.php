<?php

namespace App\Filament\Resources\Grounds;

use App\Filament\Resources\Grounds\Pages\CreateGround;
use App\Filament\Resources\Grounds\Pages\EditGround;
use App\Filament\Resources\Grounds\Pages\ListGrounds;
use App\Filament\Resources\Grounds\Schemas\GroundForm;
use App\Filament\Resources\Grounds\Tables\GroundsTable;
use App\Models\Ground;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GroundResource extends Resource
{
    protected static ?string $model = Ground::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GroundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GroundsTable::configure($table);
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
            'index' => ListGrounds::route('/'),
            'create' => CreateGround::route('/create'),
            'edit' => EditGround::route('/{record}/edit'),
        ];
    }
}
