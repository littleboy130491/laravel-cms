<?php

namespace App\Filament\Resources\PartialResource\Pages;

use App\Filament\Resources\PartialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\InteractsWithFiles;
use Illuminate\Database\Eloquent\Model;

class EditPartial extends EditRecord
{

    use InteractsWithFiles;
    protected static string $resource = PartialResource::class;

    protected static function getResourcePath(): string
    {
        return static::$resource::$resourcePath;
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn(Model $record) => url("/preview/partials/{$record->slug}"))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->after(function (Model $record) {
                    $slug = $record->slug;

                    if ($slug) {
                        $filePath = resource_path(static::getResourcePath());
                        static::deleteFile($filePath, $slug . '.blade.php');
                    }
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $data = $this->data;
        $slug = $data['slug'];
        $content = $data['content'];

        if ($slug) {
            $filePath = resource_path(static::getResourcePath());

            static::putFile($filePath, $slug . '.blade.php', $content);
        }

    }

    protected function hasUnsavedDataChangesAlert(): bool
    {
        // Disable the unsaved changes alert completely, so it will not give alert when putFile is executed
        return false;
    }
}
