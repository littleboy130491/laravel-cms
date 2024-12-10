<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Page;

class ListPages extends ListRecords
{

    use ListRecords\Concerns\Translatable;
    protected static string $resource = PageResource::class;

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
                ->badge(Page::query()->withoutTrashed()->count()),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'draft'))
                ->badge(Page::query()->where('status', 'draft')->count()),
            'trash' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->onlyTrashed()->withoutGlobalScopes())
                ->badge(Page::query()->onlyTrashed()->count()),

        ];
    }
}


