<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {

    public function up(): void
    {
        $this->migrator->add('general.site_name', 'My Site');
        $this->migrator->add('general.site_logo', '');
        $this->migrator->add('general.site_icon', '');
        $this->migrator->add('general.site_informations', 'Welcome to our site');
        $this->migrator->add('general.head_code', '');
        $this->migrator->add('general.body_code', '');
    }

    public function down(): void
    {
        $this->migrator->delete('general.site_name');
        $this->migrator->delete('general.site_logo');
        $this->migrator->delete('general.site_icon');
        $this->migrator->delete('general.site_informations');
        $this->migrator->delete('general.head_code');
        $this->migrator->delete('general.body_code');
    }
};
