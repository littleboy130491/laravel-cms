<?php

namespace App\Filament\Imports;

use App\Models\Post;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PostImporter extends Importer
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping(),
            ImportColumn::make('slug')
                ->rules(['required', 'unique:posts'])
                ->requiredMapping(),
            ImportColumn::make('content'),
            ImportColumn::make('excerpt'),
            ImportColumn::make('featured_image'),
            ImportColumn::make('author')
                ->relationship(resolveUsing: 'email'),
            ImportColumn::make('status')
                ->requiredMapping(),
            ImportColumn::make('published_at')
                ->rules(['datetime']),
            ImportColumn::make('is_featured')
                ->boolean(),
            ImportColumn::make('order_column')
                ->integer(),
            ImportColumn::make('head_code'),
            ImportColumn::make('body_code'),
            ImportColumn::make('template'),
        ];
    }

    public function resolveRecord(): ?Post
    {
        return Post::firstOrNew([
            // Update existing records, matching them by `$this->data['column_name']`
            'slug' => $this->data['slug'],
        ]);

        // return new Post();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your post import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
