<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Link;
use App\Services\SiteConfigService;

class SitemapController extends Controller
{
    public function index()
    {
        if (! SiteConfigService::enableSitemap()) {
            abort(404);
        }

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        // 首页
        $sitemap .= $this->generateUrl(
            route('home'),
            now(),
            SiteConfigService::sitemapFrequency(),
            SiteConfigService::sitemapPriority()
        );

        // 分类页面
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $sitemap .= $this->generateUrl(
                route('home').'?category='.$category->id,
                $category->updated_at,
                'weekly',
                0.7
            );
        }

        // 链接页面
        $links = Link::where('is_active', true)->get();
        foreach ($links as $link) {
            $sitemap .= $this->generateUrl(
                route('click', $link),
                $link->updated_at,
                'monthly',
                0.5
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function generateUrl(string $loc, $lastmod, string $changefreq, float $priority): string
    {
        $url = "  <url>\n";
        $url .= '    <loc>'.htmlspecialchars($loc)."</loc>\n";
        $url .= '    <lastmod>'.$lastmod->format('Y-m-d\TH:i:sP')."</lastmod>\n";
        $url .= '    <changefreq>'.$changefreq."</changefreq>\n";
        $url .= '    <priority>'.number_format($priority, 1)."</priority>\n";
        $url .= "  </url>\n";

        return $url;
    }
}
