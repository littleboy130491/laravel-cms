<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('general.additional_info', []);
    }

    public function down(): void
    {
        $this->migrator->delete('general.additional_info');
    }
};
