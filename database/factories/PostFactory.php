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
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(rand(3, 7), true),
            // 'excerpt' => $this->faker->paragraph(),
            // 'featured_image' => $this->faker->imageUrl(1200, 630),
            'author_id' => auth()->id(),
            'status' => Post::STATUS_DRAFT,
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
        ];
    }


}