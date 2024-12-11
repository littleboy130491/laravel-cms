<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Riodwanto\FilamentAceEditor\AceEditor;
use App\Filament\Exports\PostExporter;
use App\Filament\Imports\PostImporter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Awcodes\Curator\Components as Curator;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use RalphJSmit\Filament\SEO\SEO;
use Illuminate\Support\Facades\File;
use Filament\Forms\Get;
use App\Filament\Traits\HasTitleSlug;
use Filament\Resources\Concerns\Translatable;
use Illuminate\Support\Str;
class PostResource extends Resource implements HasShieldPermissions
{
    use HasTitleSlug, Translatable;
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Contents';
    protected static ?int $navigationSort = 20;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([
                    Forms\Components\Section::make()
                        ->schema([
                            ...static::titleSlugField(),
                            Forms\Components\RichEditor::make('content')
                                ->columnSpanFull()
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsDirectory('posts')
                                ->nullable(),

                            Forms\Components\Textarea::make('excerpt')
                                ->columnSpanFull()
                                ->rows(3)
                                ->nullable(),
                        ]),
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options(Post::getStatuses())
                                ->default(Post::STATUS_DRAFT)
                                ->required()
                                ->live(),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->visible(function (Get $get): bool {
                                    return $get('status') === Post::STATUS_SCHEDULED || $get('status') === Post::STATUS_PUBLISHED;
                                })
                                ->required(
                                    function (Get $get): bool {
                                        return $get('status') === Post::STATUS_SCHEDULED;
                                    }
                                ),
                            Forms\Components\Toggle::make('is_featured')
                                ->default(false)
                                ->required()
                                ->columnSpanFull(),
                            Curator\Forms\CuratorPicker::make('featured_image')
                                ->directory('static::getPluralModelLabel()')
                                ->preserveFilenames()
                                ->nullable(),
                            SelectTree::make('categories')
                                ->relationship('categories', 'title', 'parent_id')
                                ->searchable()
                                ->defaultOpenLevel(2)
                                ->createOptionForm(
                                    CategoryResource::formFields()
                                ),
                            Forms\Components\Select::make('tags')
                                ->relationship('tags', 'title')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm(TagResource::formFields()),
                            Forms\Components\Select::make('author_id')
                                ->relationship('author', 'name')
                                ->searchable()
                                ->default(auth()->id())
                                ->nullable()
                                ->preload(),
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

                        ])
                        ->grow(false),
                ])
                    ->from('md')
                    ->columnSpanFull(),


                Forms\Components\Section::make('Custom Code')
                    ->schema([
                        AceEditor::make('head_code')
                            ->mode('php')
                            ->theme('github')
                            ->darkTheme('dracula'),
                        AceEditor::make('body_code')
                            ->mode('php')
                            ->theme('github')
                            ->darkTheme('dracula'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('SEO')
                    ->schema([
                        SEO::make(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(['title', 'content'])
                    ->description(fn(Post $record): string => $record->slug),
                Curator\Tables\CuratorColumn::make('featured_image')
                    ->size(40),
                Tables\Columns\TextColumn::make('author.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => static::getModel()::getStatusColors()[$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('categories.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags.title')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_featured'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order_column')
                    ->label('Order'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_featured')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('is_featured', true)),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->excludeAttributes(['slug', 'status'])
                    ->mutateRecordDataUsing(function (Post $record, array $data): array {
                        $data['slug'] = Str::slug($record->slug . '-copy', '-', null, ['unique' => true]);
                        $data['status'] = $record::STATUS_DRAFT;
                        $data['published_at'] = null;
                        return $data;
                    })
                    ->after(function (Post $replica, Post $record): void {
                        $replica->categories()->attach($record->categories->pluck('id'));
                        $replica->tags()->attach($record->tags->pluck('id'));
                    })
                    ->form([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->helperText('Slug should be unique')
                            ->unique(ignoreRecord: true),
                    ]),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('editSelected')
                        ->icon('heroicon-m-pencil-square')
                        ->color('warning')
                        ->modalHeading('Edit Selected Records')
                        ->modalDescription('Only filled fields will be updated. Empty fields will be ignored.')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(Post::getStatuses())
                                ->live(),
                            Forms\Components\Select::make('is_featured')
                                ->options([
                                    true => 'Featured',
                                    false => 'Not featured',
                                ]),
                            Forms\Components\Select::make('author_id')
                                ->relationship('author', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->visible(function (Get $get): bool {
                                    return $get('status') === Post::STATUS_SCHEDULED || $get('status') === Post::STATUS_PUBLISHED;
                                })
                                ->required(
                                    function (Get $get): bool {
                                        return $get('status') === Post::STATUS_SCHEDULED;
                                    }
                                ),
                        ])
                        ->action(function (Collection $records, array $data) {
                            DB::transaction(function () use ($records, $data) {
                                // Filter out null and empty values, but keep false values for booleans
                                $updateData = collect($data)
                                    ->reject(function ($value) {
                                    return is_null($value) || (is_array($value) && empty($value));
                                })
                                    ->toArray();

                                $records->each(function ($record) use ($updateData, $data) {
                                    // Update regular fields if we have any
                                    if (!empty($updateData)) {
                                        $record->update($updateData);
                                    }
                                });
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // create fake / dummy data
                Tables\Actions\Action::make('postFactory')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->helperText('How many post do you want to create?')
                            ->label('Quantity')
                            ->integer(),
                    ])
                    ->action(function (array $data) {
                        Post::factory()
                            ->count($data['quantity'])
                            ->create();

                    })
                    ->color('info')
                    ->label('Create dummy')
                    ->modalHeading('Create post with dummy data')
                    ->visible(fn() => auth()->user()->can('create_dummy_post')),
                Tables\Actions\ExportAction::make()
                    ->exporter(PostExporter::class),
                Tables\Actions\ImportAction::make()
                    ->importer(PostImporter::class),
            ])
            ->reorderable('order_column')
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'create_dummy',

        ];
    }
}
