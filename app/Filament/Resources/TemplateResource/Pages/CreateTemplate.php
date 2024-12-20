<?php

namespace App\Filament\Resources\TemplateResource\Pages;

use App\Filament\Resources\TemplateResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\InteractsWithFiles;

class CreateTemplate extends CreateRecord
{
    use InteractsWithFiles;
    protected static string $resource = TemplateResource::class;
    protected static function getResourcePath(): string
    {
        return static::$resource::$resourcePath;
    }
    protected function afterCreate(): void
    {
        $data = $this->record;
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
