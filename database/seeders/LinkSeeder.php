<?php

namespace Database\Seeders;

use App\Models\Link;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'category_id' => 1,
                'title' => 'GitHub',
                'url' => 'https://github.com',
                'description' => '全球最大的代码托管平台',
                'icon' => '🐙',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'title' => 'VS Code',
                'url' => 'https://code.visualstudio.com',
                'description' => '强大的代码编辑器',
                'icon' => '💻',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'title' => 'Laravel',
                'url' => 'https://laravel.com',
                'description' => '优雅的 PHP 框架',
                'icon' => '🔴',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'title' => 'Dribbble',
                'url' => 'https://dribbble.com',
                'description' => '设计师作品分享平台',
                'icon' => '🏀',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'title' => 'Figma',
                'url' => 'https://figma.com',
                'description' => '协作式设计工具',
                'icon' => '🎭',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'title' => 'MDN Web Docs',
                'url' => 'https://developer.mozilla.org',
                'description' => 'Web 开发文档权威参考',
                'icon' => '📖',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'title' => 'Laravel 文档',
                'url' => 'https://laravel.com/docs',
                'description' => 'Laravel 官方文档',
                'icon' => '📄',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'title' => 'Cloudflare',
                'url' => 'https://cloudflare.com',
                'description' => 'CDN 和 DNS 服务',
                'icon' => '☁️',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'title' => 'Vercel',
                'url' => 'https://vercel.com',
                'description' => '前端部署平台',
                'icon' => '▲',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => 5,
                'title' => 'Stack Overflow',
                'url' => 'https://stackoverflow.com',
                'description' => '程序猿问答社区',
                'icon' => '❓',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => 5,
                'title' => 'Reddit',
                'url' => 'https://reddit.com',
                'description' => '综合性讨论社区',
                'icon' => '🤖',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($links as $link) {
            Link::create($link);
        }
    }
}
