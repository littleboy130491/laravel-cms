<?php

namespace App\Filament\Resources\PartialResource\Pages;

use App\Filament\Resources\PartialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartials extends ListRecords
{
    protected static string $resource = PartialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
