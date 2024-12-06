<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Camya\Filament\Forms\Components\TitleWithSlugInput;
use Awcodes\Curator\Components as Curator;
use App\Filament\Traits\HasTitleSlug;

class CategoryResource extends Resource
{
    use HasTitleSlug;
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Contents';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::formFields());
    }

    public static function formFields(): array
    {
        return
            [
                ...static::titleSlugField(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpan('full')
                    ->disableToolbarButtons(['attachFiles'])
                    ->nullable(),
                Curator\Forms\CuratorPicker::make('featured_image')
                    ->directory('categories')
                    ->preserveFilenames()
                    ->nullable(),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'title'),
            ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('slug')
                    ->searchable(),
                Curator\Tables\CuratorColumn::make('featured_image')
                    ->size(40),
                Tables\Columns\TextColumn::make('parent.title'),
                Tables\Columns\TextColumn::make('order_column')
                    ->label('Order'),
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
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->excludeAttributes(['slug'])
                    ->mutateRecordDataUsing(function (Category $record, array $data): array {
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->reorderable('order_column')
            ->defaultSort('title', 'asc');
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
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
