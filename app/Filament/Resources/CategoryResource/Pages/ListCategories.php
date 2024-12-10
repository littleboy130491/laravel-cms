<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;

class ListCategories extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->withoutTrashed())
                ->badge(Category::query()->withoutTrashed()->count()),
            'trash' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->onlyTrashed()->withoutGlobalScopes())
                ->badge(Category::query()->onlyTrashed()->count()),

        ];
    }
}
