<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->unique()->words(3, true),
            'url' => fake()->url(),
            'description' => fake()->sentence(),
            'icon' => '🔗',
            'icon_type' => 'emoji',
            'sort_order' => fake()->numberBetween(0, 100),
            'page_view_count' => fake()->numberBetween(0, 1000),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withSlug(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => $slug,
        ]);
    }
}
