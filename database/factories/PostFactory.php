<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->sentence(6));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'topic' => fake()->randomElement(array_keys(Post::TOPICS)),
            'focus_keyword' => fake()->words(3, true),
            'excerpt' => fake()->sentence(15),
            'body' => "## Mở đầu\n".fake()->paragraph()."\n## Kết luận\n".fake()->paragraph(),
            'status' => 'published',
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }

    /**
     * Published by the admin, but dated in the future — must stay hidden
     * from the storefront until that time arrives.
     */
    public function scheduled(): static
    {
        return $this->state(fn () => ['status' => 'published', 'published_at' => now()->addWeek()]);
    }
}
