<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Tag;

class ListTags extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = TagResource::class;

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
                ->badge(Tag::query()->withoutTrashed()->count()),
            'trash' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->onlyTrashed()->withoutGlobalScopes())
                ->badge(Tag::query()->onlyTrashed()->count()),

        ];
    }
}
