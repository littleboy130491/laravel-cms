<?php

namespace App\Console\Commands;

use App\Models\Partial;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SyncPartials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-partials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync template files from views/components/partials to Partial Resource';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if Partial model exists
        if (!class_exists(Partial::class)) {
            $this->error('Partial model does not exist. Please create the model first.');
            return 1;
        }

        // Check if partials table exists in database
        if (!Schema::hasTable('partials')) {
            $this->error('Partials table does not exist. Please run migrations first.');
            return 1;
        }

        $path = resource_path('views/components/partials');

        // Check if directory exists
        if (!File::isDirectory($path)) {
            $this->error("Directory not found: {$path}");
            return 1;
        }

        // Get all .blade.php files
        $files = File::files($path);
        $count = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // Get filename without extension
            $filename = $file->getBasename('.blade.php');

            // Skip if template already exists
            if (Partial::where('slug', Str::slug($filename))->exists()) {
                $this->warn("Partial '{$filename}' already exists, skipping...");
                continue;
            }

            // Read file content
            $content = File::get($file->getPathname());

            // Create template
            Partial::create([
                'name' => $filename,
                'slug' => Str::slug($filename),
                'content' => $content,
                'is_active' => true,
            ]);

            $this->info("Created partial: {$filename}");
            $count++;
        }

        $this->info("Successfully created {$count} partials.");
        return 0;
    }
}
