<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    public string $site_logo;
    public string $site_icon;
    public string $site_informations;
    public string $head_code;
    public string $body_code;
    public static function group(): string
    {
        return 'general';
    }
}