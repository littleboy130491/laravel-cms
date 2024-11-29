<?php

namespace App\Filament\Exports;

use App\Models\Submission;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SubmissionExporter extends Exporter
{
    protected static ?string $model = Submission::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fields.name')
                ->label('Name'),
            ExportColumn::make('fields.phone')
                ->label('Phone'),
            ExportColumn::make('fields.email')
                ->label('Email'),
            ExportColumn::make('fields.message')
                ->label('Message'),
            ExportColumn::make('created_at')
                ->label('Created at')
                ->state(function ($record) {
                    return $record->created_at;
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your submission export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
