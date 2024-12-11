<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Riodwanto\FilamentAceEditor\AceEditor;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use App\Filament\Traits\InteractsWithFiles;
use App\Filament\Traits\HasTitleSlug;
class TemplateResource extends Resource
{

    use InteractsWithFiles, HasTitleSlug;
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'Contents';
    protected static ?int $navigationSort = 100;

    public static string $resourcePath = 'views/components/templates/';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...static::titleSlugField(title: 'name', readOnly: true),

                AceEditor::make('content')
                    ->label('Template code')
                    ->helperText(new HtmlString('You can put HTML, blade or components here.<br>
                            Use <b>{!! Blade::render($content, [\'page\' => $page]) !!}</b> to display HTML content from page record.'))
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
                    ->getStateUsing(fn(Template $record): string => '<x-templates.' . $record->slug . '/>')
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
                    ->url(fn(Template $record) => url("/preview/templates/{$record->slug}"))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Template $record) {
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }

}
