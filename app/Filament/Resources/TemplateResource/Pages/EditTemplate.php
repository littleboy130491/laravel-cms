<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use App\Filament\Traits\InteractsWithFiles;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
class EditTemplate extends EditRecord
{
    use InteractsWithFiles;
    protected static string $resource = TemplateResource::class;
    protected static bool $unsavedDataChangesAlert = true;

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
                ->url(fn(Model $record) => url("/preview/{$record->slug}"))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->after(function (Model $record) {
                    $slug = $record->slug;

                    if ($slug) {
                        $filePath = resource_path(static::getResourcePath() . $slug . '.blade.php');
                        static::deleteFile($filePath);
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
            $filePath = resource_path(static::getResourcePath() . $slug . '.blade.php');

            static::putFile($filePath, $content);
        }

    }

    protected function hasUnsavedDataChangesAlert(): bool
    {
        // Disable the unsaved changes alert completely, so it will not give alert when putFile is executed
        return false;
    }
}