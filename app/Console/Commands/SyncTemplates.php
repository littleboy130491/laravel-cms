<?php

namespace App\Console\Commands;

use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SyncTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync template files from views/components/templates to Template Resource';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if Template model exists
        if (!class_exists(Template::class)) {
            $this->error('Template model does not exist. Please create the model first.');
            return 1;
        }

        // Check if templates table exists in database
        if (!Schema::hasTable('templates')) {
            $this->error('Templates table does not exist. Please run migrations first.');
            return 1;
        }

        $path = resource_path('views/components/templates');

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
            if (Template::where('slug', Str::slug($filename))->exists()) {
                $this->warn("Template '{$filename}' already exists, skipping...");
                continue;
            }

            // Read file content
            $content = File::get($file->getPathname());

            // Create template
            Template::create([
                'name' => $filename,
                'slug' => Str::slug($filename),
                'content' => $content,
                'is_active' => true,
            ]);

            $this->info("Created template: {$filename}");
            $count++;
        }

        $this->info("Successfully created {$count} templates.");
        return 0;
    }
}
