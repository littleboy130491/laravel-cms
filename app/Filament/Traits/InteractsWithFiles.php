<?php

namespace App\Filament\Traits;

use Illuminate\Support\Facades\File;
trait InteractsWithFiles
{
    private static function checkFileExists(string $filePath): bool
    {
        return (File::exists($filePath));

    }
    private static function getFile(string $filePath): ?string
    {
        return File::get($filePath);
    }
    private static function deleteFile(string $filePath): void
    {
        File::delete($filePath);
    }

    private static function putFile(string $filePath, ?string $content): void
    {
        File::put($filePath, $content);
    }

}