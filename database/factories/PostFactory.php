<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'community_id' => null,
            'type' => 'question',
            'title' => fake()->sentence(6),
            'body' => fake()->paragraphs(3, true),
            'status' => 'published',
            'is_pinned' => false,
            'published_at' => now(),
            'deleted_at' => null,
        ];
    }
}
