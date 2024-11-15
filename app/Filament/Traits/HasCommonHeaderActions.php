<?php

namespace App\Filament\Traits;

use Filament\Actions;

trait HasCommonHeaderActions
{
    protected function commonHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('create_new')
                ->url(static::$resource::getUrl('create'))
                ->color('success'),
        ];
    }
}