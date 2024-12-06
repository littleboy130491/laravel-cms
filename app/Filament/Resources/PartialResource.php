<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartialResource\Pages;
use App\Models\Partial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Traits\InteractsWithFiles;
use Riodwanto\FilamentAceEditor\AceEditor;
use App\Filament\Traits\HasTitleSlug;

class PartialResource extends Resource
{
    use InteractsWithFiles, HasTitleSlug;
    protected static ?string $model = Partial::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'Contents';
    protected static ?int $navigationSort = 110;

    public static string $resourcePath = 'views/components/partials/';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...static::titleSlugField(title: 'name', readOnly: true),

                AceEditor::make('content')
                    ->label('Template code')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('description')
                    ->helperText('For notes only')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('component_name')
                    ->getStateUsing(fn(Partial $record): string => '<x-partials.' . $record->slug . '/>')
                    ->icon('heroicon-o-clipboard')
                    ->iconPosition(IconPosition::After)
                    ->iconColor('primary')
                    ->copyable()
                    ->copyMessage('Component name copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Partial $record) => url("/preview/partials/{$record->slug}"))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Partial $record) {
                        $slug = $record->slug;

                        if ($slug) {
                            $filePath = resource_path(static::$resourcePath);
                            static::deleteFile($filePath, $slug . '.blade.php');
                        }
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                // First delete the record
                                $record->delete();

                                // Then handle file deletion
                                $slug = $record->slug;
                                if ($slug) {
                                    $filePath = resource_path(static::$resourcePath);
                                    static::deleteFile($filePath, $slug . '.blade.php');
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartials::route('/'),
            'create' => Pages\CreatePartial::route('/create'),
            'edit' => Pages\EditPartial::route('/{record}/edit'),
        ];
    }
}
