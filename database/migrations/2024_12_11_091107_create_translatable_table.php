<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $currentLocale = config('app.locale', 'en');
        $availableLanguages = config('app.lang_available', ['en']);
        $languages = array_unique(
            array_merge([$currentLocale], $availableLanguages)
        );

        Schema::create('translatable', function (Blueprint $table) use ($languages) {
            $table->id();
            $table->morphs('translatable');  // This creates translatable_type and translatable_id

            // Create language columns with foreign keys to the same parent table
            foreach ($languages as $lang) {
                $table->unsignedBigInteger('lang_' . $lang . '_id')->nullable();

                // Here's the important part - we add a foreign key constraint
                // that references back to the original table dynamically
                $table->foreign('lang_' . $lang . '_id')
                    ->references('id')
                    ->on(DB::raw('(SELECT 1) as dummy_table'))  // This will be replaced at runtime
                    ->nullOnDelete();  // If the referenced record is deleted, set this to null
            }

            $table->timestamps();

            // Add a unique constraint to prevent duplicate mappings
            $table->unique(['translatable_type', 'translatable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translatable');
    }
};