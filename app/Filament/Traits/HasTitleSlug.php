<?php

namespace App\Filament\Traits;

use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms;

trait HasTitleSlug
{
    protected static function titleSlugField(string $title = 'title', string $slug = 'slug', bool $readOnly = false): array
    {
        return [
            Forms\Components\TextInput::make($title)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state, string $operation) {
                    if ($operation === 'edit')
                        return;

                    $set('slug', Str::slug($state));
                })
                ->required(),

            Forms\Components\TextInput::make($slug)
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->readOnly($readOnly)
                ->live(onBlur: true)
                ->rules(['alpha_dash'])
                ->dehydrateStateUsing(fn($state) => Str::slug($state))
                ->afterStateUpdated(function (Forms\Components\TextInput $component, ?string $state) {
                    $component->state(Str::slug($state));
                }),
        ];
    }
}