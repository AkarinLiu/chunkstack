<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => '开发工具',
                'slug' => 'dev-tools',
                'description' => '开发过程中常用的工具和资源',
                'sort_order' => 1,
                'icon' => '🛠️',
                'is_active' => true,
            ],
            [
                'name' => '设计资源',
                'slug' => 'design-resources',
                'description' => '设计灵感和素材资源',
                'sort_order' => 2,
                'icon' => '🎨',
                'is_active' => true,
            ],
            [
                'name' => '学习教程',
                'slug' => 'tutorials',
                'description' => '优质的学习教程和文档',
                'sort_order' => 3,
                'icon' => '📚',
                'is_active' => true,
            ],
            [
                'name' => '实用服务',
                'slug' => 'services',
                'description' => '实用的在线服务',
                'sort_order' => 4,
                'icon' => '🚀',
                'is_active' => true,
            ],
            [
                'name' => '社区论坛',
                'slug' => 'community',
                'description' => '技术社区和论坛',
                'sort_order' => 5,
                'icon' => '💬',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
