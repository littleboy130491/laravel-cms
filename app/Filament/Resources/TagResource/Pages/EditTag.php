<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use App\Filament\Traits\HasCommonHeaderActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Tag;

class EditTag extends EditRecord
{
    use HasCommonHeaderActions, EditRecord\Concerns\Translatable;
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\Action::make('view')
                ->url(fn(Tag $record): string =>
                    '/' . $this->getActiveActionsLocale() . '/' . Tag::$slugPath . '/' . $record->slug)
                ->openUrlInNewTab()
                ->color('gray'),
            ...($this->commonHeaderActions()),
        ];
    }
}
