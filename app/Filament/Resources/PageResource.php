<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use App\Models\Page;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Riodwanto\FilamentAceEditor\AceEditor;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\File;
use RalphJSmit\Filament\SEO\SEO;
use App\Filament\Traits\HasTitleSlug;
use Awcodes\Curator\Components as Curator;
use Filament\Resources\Concerns\Translatable;
class PageResource extends Resource
{
    use HasTitleSlug, Translatable;
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Contents';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                    Forms\Components\Split::make([
                        Forms\Components\Section::make()
                            ->schema([
                                ...static::titleSlugField(),
                                AceEditor::make('content')
                                    ->mode('php')
                                    ->helperText(new HtmlString('You can put HTML, blade or components here. Basic @ directives are supported.
                        <br>You can access the value form fields using {{$page->field_name}}. Ex: {{$page->title}}'))
                                    ->columnSpanFull(),
                                Forms\Components\KeyValue::make('meta')
                                    ->reorderable()
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('excerpt')
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->nullable(),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published'
                                    ])
                                    ->default('published')
                                    ->required(),
                                Forms\Components\Select::make('template')
                                    ->options(function () {
                                        $path = resource_path('views/components/templates');
                                        $files = File::files($path);

                                        return collect($files)->mapWithKeys(function ($file) {
                                            $filename = $file->getFilename();
                                            return [$filename => $filename];
                                        })->toArray();
                                    })
                                    ->searchable()
                                    ->helperText('Leave empty for using default template'),
                                Curator\Forms\CuratorPicker::make('featured_image')
                                    ->directory('static::getPluralModelLabel()')
                                    ->preserveFilenames()
                                    ->nullable(),
                            ])
                            ->grow(false),
                    ])
                        ->from('md')
                        ->columnSpanFull(),
                    Forms\Components\Section::make('Custom Code')
                        ->columns(2)
                        ->schema([
                            AceEditor::make('head_code')
                                ->mode('html')
                                ->helperText('This code will be rendered before </head>'),
                            AceEditor::make('body_code')
                                ->mode('html')
                                ->helperText('This code will be rendered before </body>'),
                        ]),
                    Forms\Components\Section::make('SEO')
                        ->schema([
                            SEO::make(),
                        ]),
                ]
            );

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(['title', 'content'])
                    ->description(fn(Page $record): string => $record->slug),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                    }),

            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->excludeAttributes(['slug'])
                    ->mutateRecordDataUsing(function (Page $record, array $data): array {
                        $data['slug'] = $record->slug . '-copy';
                        return $data;
                    })
                    ->form([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->helperText('Slug should be unique')
                            ->unique(),
                    ]),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
