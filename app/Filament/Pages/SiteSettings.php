<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components;
use Awcodes\Curator\Components as Curator;
use Riodwanto\FilamentAceEditor\AceEditor;

class SiteSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 0;
    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('site_name'),
                Components\TextInput::make('site_informations'),

                Curator\Forms\CuratorPicker::make('site_logo')
                    ->preserveFilenames(),

                Curator\Forms\CuratorPicker::make('site_icon')
                    ->preserveFilenames(),

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

            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        return array_map(function ($value) {
            // Convert null to empty string
            if (is_null($value)) {
                return '';
            }

            // Handle nested arrays recursively
            if (is_array($value)) {
                return array_map(function ($nestedValue) {
                    return is_null($nestedValue) ? '' : $nestedValue;
                }, $value);
            }

            return $value;
        }, $data);
    }
}
