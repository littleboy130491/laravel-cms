<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CmsSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cms-setup
      {--email= : The email address for the admin user}
                          {--name= : The name for the admin user}
                          {--password= : The password for the admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup CMS with Filament, Shield, templates and partials';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        // Check if Filament is installed
        if (!class_exists(\Filament\FilamentServiceProvider::class)) {
            $this->error('Filament is not installed. Please install Filament first.');
            return 1;
        }

        // Check if Shield is installed
        if (!class_exists(\BezhanSalleh\FilamentShield\FilamentShieldServiceProvider::class)) {
            $this->error('Filament Shield is not installed. Please install Shield first.');
            return 1;
        }

        // Check if users table exists
        if (!Schema::hasTable('users')) {
            $this->error('Users table does not exist. Please run migrations first.');
            return 1;
        }

        // Check if Shield tables exist
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            $this->error('Shield tables do not exist. Please run shield migrations first.');
            $this->info('You can run: php artisan shield:install and then php artisan migrate');
            return 1;
        }

        $this->info('Starting CMS setup...');

        // Get or prompt for user details
        $email = $this->option('email') ?: $this->ask('What is the admin email?');
        $name = $this->option('name') ?: $this->ask('What is the admin name?');
        $password = $this->option('password') ?: $this->secret('What is the admin password?');

        // Create Filament user
        $this->info('Creating Filament admin user...');
        $this->call('make:filament-user', [
            '--email' => $email,
            '--name' => $name,
            '--password' => $password,
        ]);

        // Create super admin
        $this->info('Setting up super admin privileges...');
        $this->call('shield:super-admin');

        // Generate Shield policies
        $this->info('Generating Shield policies...');
        $this->call('shield:generate', [
            '--all' => true,
        ]);

        // Sync templates
        $this->info('Sync templates from views');
        $this->call('app:sync-templates');

        // Sync partials
        $this->info('Sync templates from partials');
        $this->call('app:sync-partials');

        $this->info('✅ CMS setup completed successfully!');

        // Display summary
        $this->info('You can now login to the admin panel with:');
        $this->line("Email: {$email}");

        return 0;

    }
}
