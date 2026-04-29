<?php

use App\Models\Category;
use App\Models\Link;
use App\Models\Tag;

test('link slug is auto-generated from title', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $link = Link::create([
        'category_id' => $category->id,
        'title' => 'My Test Link',
        'url' => 'https://example.com',
    ]);

    expect($link->slug)->toBe('my-test-link');
});

test('link slug handles duplicate titles', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $link1 = Link::create([
        'category_id' => $category->id,
        'title' => 'Same Title',
        'url' => 'https://example1.com',
    ]);

    $link2 = Link::create([
        'category_id' => $category->id,
        'title' => 'Same Title',
        'url' => 'https://example2.com',
    ]);

    expect($link1->slug)->toBe('same-title');
    expect($link2->slug)->toBe('same-title-1');
});

test('link slug is regenerated when title changes', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $link = Link::create([
        'category_id' => $category->id,
        'title' => 'Old Title',
        'url' => 'https://example.com',
    ]);

    expect($link->slug)->toBe('old-title');

    $link->update(['title' => 'New Title']);

    expect($link->fresh()->slug)->toBe('new-title');
});

test('link detail page displays link information', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $tag = Tag::create([
        'name' => 'TestTag',
        'slug' => 'testtag',
        'color' => '#ff0000',
    ]);

    $link = Link::create([
        'category_id' => $category->id,
        'title' => 'My Test Link',
        'url' => 'https://example.com',
        'description' => 'A great description here',
        'slug' => 'my-test-link',
    ]);

    $link->tags()->attach($tag->id);

    $response = $this->get('/link/my-test-link');

    $response->assertStatus(200);
    $response->assertSee('My Test Link');
});

test('link detail page returns 404 for non-existent slug', function () {
    $response = $this->get('/link/non-existent-slug');

    $response->assertStatus(404);
});

test('api links index returns categories with active links', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    Link::create([
        'category_id' => $category->id,
        'title' => 'Test Link',
        'url' => 'https://example.com',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $response = $this->getJson('/api/links');

    $response->assertStatus(200);
    $response->assertJsonPath('categories.0.name', 'Test Category');
    $response->assertJsonPath('categories.0.active_links.0.title', 'Test Link');
});

test('api links index supports search', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    Link::create([
        'category_id' => $category->id,
        'title' => 'Laravel Tips',
        'url' => 'https://laravel.com',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    Link::create([
        'category_id' => $category->id,
        'title' => 'Vue.js Guide',
        'url' => 'https://vuejs.org',
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $response = $this->getJson('/api/links?q=Laravel');

    $response->assertStatus(200);
    $response->assertJsonCount(1, 'links');
    $response->assertJsonPath('links.0.title', 'Laravel Tips');
});

test('api links show returns single link', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $link = Link::create([
        'category_id' => $category->id,
        'title' => 'Test Link',
        'url' => 'https://example.com',
        'slug' => 'test-link',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $response = $this->getJson('/api/links/test-link');

    $response->assertStatus(200);
    $response->assertJsonPath('link.title', 'Test Link');
    $response->assertJsonPath('link.slug', 'test-link');
});

test('api link view increments page_view_count', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    $link = Link::create([
        'category_id' => $category->id,
        'title' => 'Test Link',
        'url' => 'https://example.com',
        'slug' => 'test-link',
        'is_active' => true,
        'sort_order' => 0,
    ]);

    expect($link->page_view_count)->toBe(0);

    $this->postJson('/api/link-views/test-link')->assertStatus(200);

    expect($link->fresh()->page_view_count)->toBe(1);
});
