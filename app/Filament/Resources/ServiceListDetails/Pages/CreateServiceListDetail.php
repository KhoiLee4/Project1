<?php

namespace App\Filament\Resources\ServiceListDetails\Pages;

use App\Filament\Resources\ServiceListDetails\ServiceListDetailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceListDetail extends CreateRecord
{
    protected static string $resource = ServiceListDetailResource::class;
}
