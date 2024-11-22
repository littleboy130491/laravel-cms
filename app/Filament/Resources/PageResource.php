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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Riodwanto\FilamentAceEditor\AceEditor;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\File;
use RalphJSmit\Filament\SEO\SEO;
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Contents';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Section::make('Title')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            })
                            ->required(),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),
                Forms\Components\Section::make('Content')
                    ->schema([
                        AceEditor::make('content')
                            ->mode('php')
                            ->helperText(new HtmlString('You can put HTML, blade or components here. @ characters are not supported.
                            <br>You can access the value form fields using {{$page->field_name}}. Ex: {{$page->title}}')),
                        Forms\Components\KeyValue::make('meta')
                            ->reorderable(),
                    ]),
                Forms\Components\Section::make('Additional Code')
                    ->columns(2)
                    ->schema([
                        AceEditor::make('head_code')
                            ->mode('html')
                            ->helperText('This code will be rendered before </head>'),
                        AceEditor::make('body_code')
                            ->mode('html')
                            ->helperText('This code will be rendered before </body>'),
                    ]),
                Forms\Components\Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published'
                            ])
                            ->default('draft')
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
                        Forms\Components\Section::make('SEO')
                            ->schema([
                                SEO::make(),
                            ]),

                    ])
            ]);

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
                Tables\Actions\EditAction::make(),
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
