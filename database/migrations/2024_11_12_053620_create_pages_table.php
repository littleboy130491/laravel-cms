<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Core page content
            $table->json('title');
            $table->json('slug')->unique();
            $table->json('meta')->nullable();
            $table->longText('content')->nullable();
            $table->json('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            // Page status
            $table->enum('status', ['draft', 'published'])
                ->default('draft');

            // Custom code injection
            $table->text('head_code')->nullable();
            $table->text('body_code')->nullable();

            // Template selection
            $table->string('template')->nullable()->index();

            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
