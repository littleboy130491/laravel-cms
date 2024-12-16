<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Traits\HasCommonHeaderActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Category;
class EditCategory extends EditRecord
{
    use HasCommonHeaderActions, EditRecord\Concerns\Translatable;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\Action::make('view')
                ->url(fn(Category $record): string =>
                    '/' . $this->getActiveActionsLocale() . '/' . Category::$slugPath . '/' . $record->slug)
                ->openUrlInNewTab()
                ->color('gray'),
            ...($this->commonHeaderActions()),
        ];
    }
}
