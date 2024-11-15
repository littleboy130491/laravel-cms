<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();
        $isPublished = $this->faker->boolean(70); // 70% chance of being published
        $publishedDate = $isPublished ? 
            $this->faker->dateTimeBetween('-1 year', 'now') : 
            null;
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(rand(3, 7), true),
            // 'excerpt' => $this->faker->paragraph(),
            // 'featured_image' => $this->faker->imageUrl(1200, 630),
            'author_id' => auth()->id(),
            'status' => $this->faker->randomElement(array_keys(Post::getStatuses())),
            'published_at' => $publishedDate,
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
                'published_at' => Carbon::now(),
            ];
        });
    }

    /**
     * Indicate that the post is drafted.
     */
    public function draft(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'published_at' => null,
            ];
        });
    }

    /**
     * Indicate that the post is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'scheduled',
                'published_at' => Carbon::now()->addDays(rand(1, 30)),
            ];
        });
    }

    /**
     * Indicate that the post is featured.
     */
    public function featured(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }
    
    /**
     * Configure the factory to attach categories.
     */
    public function withCategories(int $count = 1): static
    {
        return $this->afterCreating(function (Post $post) use ($count) {
            $post->categories()->attach(
                \App\Models\Category::factory()->count($count)->create()
            );
        });
    }

    /**
     * Configure the factory to attach tags.
     */
    public function withTags(int $count = 3): static
    {
        return $this->afterCreating(function (Post $post) use ($count) {
            $post->tags()->attach(
                \App\Models\Tag::factory()->count($count)->create()
            );
        });
    }
}