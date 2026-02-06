<?php

use App\Models\User;
use App\Services\SiteConfigService;

test('site settings page is accessible to admin users', function () {
    $user = User::factory()->create(['is_admin' => true]);

    $this->actingAs($user)
        ->get(route('admin.settings.index'))
        ->assertStatus(200)
        ->assertSee('站点设置');
});

test('site settings can be updated', function () {
    $user = User::factory()->create(['is_admin' => true]);

    $data = [
        'site.name' => '测试网站',
        'site.description' => '这是一个测试网站的描述',
        'site.url' => 'https://test.example.com',
        'site.enable_sitemap' => true,
        'site.sitemap_frequency' => 'weekly',
        'site.sitemap_priority' => 0.9,
    ];

    $this->actingAs($user)
        ->put(route('admin.settings.update'), $data)
        ->assertRedirect(route('admin.settings.index'))
        ->assertSessionHas('success');

    $this->assertEquals('测试网站', SiteConfigService::siteName());
    $this->assertEquals('这是一个测试网站的描述', SiteConfigService::siteDescription());
    $this->assertEquals('https://test.example.com', SiteConfigService::siteUrl());
    $this->assertTrue(SiteConfigService::enableSitemap());
    $this->assertEquals('weekly', SiteConfigService::sitemapFrequency());
    $this->assertEquals(0.9, SiteConfigService::sitemapPriority());
});

test('sitemap is accessible when enabled', function () {
    SiteConfigService::set('site.enable_sitemap', true);

    $this->get(route('sitemap'))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'application/xml');
});

test('sitemap returns 404 when disabled', function () {
    SiteConfigService::set('site.enable_sitemap', false);

    $this->get(route('sitemap'))
        ->assertStatus(404);
});
