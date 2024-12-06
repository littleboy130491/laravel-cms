<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Filament\Traits\HasCommonHeaderActions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
class EditPost extends EditRecord
{
    use HasCommonHeaderActions, EditRecord\Concerns\Translatable;
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\Action::make('view')
                ->url(fn(Model $record): string => '/posts/' . $record->slug)
                ->openUrlInNewTab()
                ->color('gray'),
            ...($this->commonHeaderActions()),
        ];
    }
}
