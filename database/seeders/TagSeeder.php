<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => '开源', 'slug' => 'open-source', 'color' => '#3B82F6'],
            ['name' => '免费', 'slug' => 'free', 'color' => '#10B981'],
            ['name' => '付费', 'slug' => 'paid', 'color' => '#F59E0B'],
            ['name' => '中文', 'slug' => 'chinese', 'color' => '#EF4444'],
            ['name' => '英文', 'slug' => 'english', 'color' => '#6366F1'],
            ['name' => '推荐', 'slug' => 'recommended', 'color' => '#EC4899'],
            ['name' => '工具', 'slug' => 'tool', 'color' => '#8B5CF6'],
            ['name' => '教程', 'slug' => 'tutorial', 'color' => '#14B8A6'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
