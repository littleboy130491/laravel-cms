<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use App\Filament\Traits\HasCommonHeaderActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    use HasCommonHeaderActions;
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...($this->commonHeaderActions()),
        ];
    }
}
