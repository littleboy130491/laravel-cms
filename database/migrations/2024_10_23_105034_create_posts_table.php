<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('slug')->unique();
            $table->json('content')->nullable();
            $table->json('excerpt')->nullable();
            $table->string('featured_image')->nullable();

            // Author relationship
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status and publishing
            $table->enum('status', ['draft', 'scheduled', 'published'])
                ->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('order_column')->nullable()->default(0);

            // Custom code injection
            $table->longText('head_code')->nullable();
            $table->longText('body_code')->nullable();

            // Template
            $table->string('template')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot tables remain the same
        Schema::create('category_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('posts');
    }
};