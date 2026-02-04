<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkRequest;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(): View
    {
        $links = Link::query()
            ->with('category')
            ->with('tags')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.links.index', compact('links'));
    }

    public function create(): View
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();

        return view('admin.links.create', compact('categories', 'tags'));
    }

    public function store(LinkRequest $request): RedirectResponse
    {
        $link = Link::create($request->validated());

        if ($request->has('tags')) {
            $link->tags()->sync($request->tags);
        }

        return redirect()->route('admin.links.index')
            ->with('success', '链接创建成功');
    }

    public function edit(Link $link): View
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();

        return view('admin.links.edit', compact('link', 'categories', 'tags'));
    }

    public function update(LinkRequest $request, Link $link): RedirectResponse
    {
        $link->update($request->validated());

        if ($request->has('tags')) {
            $link->tags()->sync($request->tags);
        } else {
            $link->tags()->detach();
        }

        return redirect()->route('admin.links.index')
            ->with('success', '链接更新成功');
    }

    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();

        return redirect()->route('admin.links.index')
            ->with('success', '链接删除成功');
    }
}
