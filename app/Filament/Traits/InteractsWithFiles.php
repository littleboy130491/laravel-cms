<?php

namespace App\Filament\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

trait InteractsWithFiles
{
    /**
     * Check if a directory exists.
     *
     * @param string $path Directory path
     * @return bool
     */
    private static function directoryExists(string $path): bool
    {
        return File::isDirectory(self::normalizePath($path));
    }

    /**
     * Create a directory if it doesn't exist.
     *
     * @param string $path Directory path
     * @param int $permissions Directory permissions (default: 0755)
     * @param bool $recursive Create parent directories if they don't exist
     * @throws RuntimeException If directory creation fails
     * @return void
     */
    private static function ensureDirectoryExists(
        string $path,
        int $permissions = 0755,
        bool $recursive = true
    ): void {
        $path = self::normalizePath($path);

        if (!self::directoryExists($path)) {
            if (!File::makeDirectory($path, $permissions, $recursive)) {
                throw new RuntimeException("Failed to create directory: {$path}");
            }
        }
    }

    /**
     * Check if a file exists.
     *
     * @param string $path Directory path
     * @param string $filename Filename
     * @return bool
     */
    private static function fileExists(string $path, string $filename): bool
    {
        return File::exists(self::buildFilePath($path, $filename));
    }

    /**
     * Get file contents.
     *
     * @param string $path Directory path
     * @param string $filename Filename
     * @throws RuntimeException If file doesn't exist or is not readable
     * @return string
     */
    private static function getFile(string $path, string $filename): string
    {
        $fullPath = self::buildFilePath($path, $filename);

        if (!self::fileExists($path, $filename)) {
            throw new RuntimeException("File does not exist: {$fullPath}");
        }

        try {
            return File::get($fullPath);
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to read file: {$fullPath}", 0, $e);
        }
    }

    /**
     * Delete a file.
     *
     * @param string $path Directory path
     * @param string $filename Filename
     * @throws RuntimeException If file deletion fails
     * @return bool True if file was deleted, false if it didn't exist
     */
    private static function deleteFile(string $path, string $filename): bool
    {
        $fullPath = self::buildFilePath($path, $filename);

        if (!self::fileExists($path, $filename)) {
            return false;
        }

        if (!File::delete($fullPath)) {
            throw new RuntimeException("Failed to delete file: {$fullPath}");
        }

        return true;
    }

    /**
     * Save content to a file.
     *
     * @param string $path Directory path
     * @param string $filename Filename
     * @param string|null $content File content
     * @throws RuntimeException If file creation fails
     * @return void
     */
    private static function putFile(string $path, string $filename, ?string $content): void
    {
        self::ensureDirectoryExists($path);

        $fullPath = self::buildFilePath($path, $filename);

        try {
            File::put($fullPath, $content ?? '');
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to write file: {$fullPath}", 0, $e);
        }
    }

    /**
     * Get file size in bytes.
     *
     * @param string $path Directory path
     * @param string $filename Filename
     * @throws RuntimeException If file doesn't exist
     * @return int
     */
    private static function getFileSize(string $path, string $filename): int
    {
        $fullPath = self::buildFilePath($path, $filename);

        if (!self::fileExists($path, $filename)) {
            throw new RuntimeException("File does not exist: {$fullPath}");
        }

        return File::size($fullPath);
    }

    /**
     * Build a complete file path from directory and filename.
     *
     * @param string $path Directory path
     * @param string $filename Filename
     * @throws InvalidArgumentException If filename contains directory traversal
     * @return string
     */
    private static function buildFilePath(string $path, string $filename): string
    {
        // Prevent directory traversal
        if (Str::contains($filename, ['/', '\\', '..'])) {
            throw new InvalidArgumentException('Invalid filename provided');
        }

        return self::normalizePath($path) . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Normalize a path by removing extra slashes and ensuring proper directory separators.
     *
     * @param string $path Path to normalize
     * @return string
     */
    private static function normalizePath(string $path): string
    {
        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }
}