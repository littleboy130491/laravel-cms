<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Filament\Traits\HasCommonHeaderActions;
use App\Models\Page;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HasCommonHeaderActions, EditRecord\Concerns\Translatable;
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\Action::make('view')
                ->url(fn(Page $record): string =>
                    '/' . $this->getActiveActionsLocale() . '/' . $record->slug)
                ->openUrlInNewTab()
                ->color('gray'),
            ...($this->commonHeaderActions()),

        ];
    }

    protected function afterSave(): void
    {
        // Runs after the form fields are saved to the database.

    }


}


