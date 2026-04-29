<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinkRequest;
use App\Models\Category;
use App\Models\Link;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $query = Link::query()
            ->with('category')
            ->with('tags');

        if ($request->filled('q')) {
            $q = $request->get('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('title', 'like', "%{$q}%")
                    ->orWhere('url', 'like', "%{$q}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($qry) use ($request) {
                $qry->where('id', $request->get('tag'));
            });
        }

        if ($request->has('is_active') && $request->get('is_active') !== '') {
            $query->where('is_active', $request->get('is_active'));
        }

        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['sort_order', 'page_view_count', 'click_count', 'created_at', 'title'];
        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('sort_order');
        }

        $links = $query->paginate(20)->withQueryString();
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.links.index', compact('links', 'categories', 'tags'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $tags = Tag::all();

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
        $categories = Category::all();
        $tags = Tag::all();

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
