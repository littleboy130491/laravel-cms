<?php

namespace App\Filament\Resources\PartialResource\Pages;

use App\Filament\Resources\PartialResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\InteractsWithFiles;

class CreatePartial extends CreateRecord
{
    use InteractsWithFiles;
    protected static string $resource = PartialResource::class;

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
