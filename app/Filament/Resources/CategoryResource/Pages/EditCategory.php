<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Traits\HasCommonHeaderActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use HasCommonHeaderActions;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...($this->commonHeaderActions()),
        ];
    }
}
