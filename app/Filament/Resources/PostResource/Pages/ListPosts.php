<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Post;


class ListPosts extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = PostResource::class;

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
                ->badge(Post::query()->withoutTrashed()->count()),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'draft'))
                ->badge(Post::query()->where('status', 'draft')->count()),
            'trash' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->onlyTrashed()->withoutGlobalScopes())
                ->badge(Post::query()->onlyTrashed()->count()),

        ];
    }

}
