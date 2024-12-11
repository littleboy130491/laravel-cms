<?php

namespace App\Filament\Traits;
use App\Settings\GeneralSettings;
use Filament\Actions;
use Filament\Resources\Pages;

trait HandlesTranslation
{
    public function bootedTraits(): void
    {
        $settings = app(GeneralSettings::class);

        if ($settings->enable_translation) {
            $traitClass = class_basename($this) === 'CreateRecord'
                ? Pages\CreateRecord\Concerns\Translatable::class
                : (class_basename($this) === 'EditRecord'
                    ? Pages\EditRecord\Concerns\Translatable::class
                    : Pages\ListRecords\Concerns\Translatable::class);

            $this->bootTraitInstance($traitClass, 'bootTranslatable');
        }
    }

    protected function getTranslationHeaderActions(): array
    {
        $settings = app(GeneralSettings::class);

        return $settings->enable_translation
            ? [Actions\LocaleSwitcher::make()->locales($settings->available_locales)]
            : [];
    }
}