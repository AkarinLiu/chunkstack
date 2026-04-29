<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->get('q');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['sort_order', 'page_view_count', 'click_count', 'title', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'sort_order';
        }
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        if ($query) {
            $links = Link::query()
                ->active()
                ->withCategory()
                ->with('tags')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhereHas('tags', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        });
                })
                ->orderBy($sort, $direction)
                ->get();
            $categories = collect();
        } else {
            $categories = Category::query()
                ->with('activeLinks.tags')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->each(function (Category $category) use ($sort, $direction) {
                    $sorted = $category->activeLinks->sortBy([
                        [$sort, $direction === 'desc' ? false : true],
                    ])->values();
                    $category->setRelation('activeLinks', $sorted);
                });
            $links = collect();
        }

        return view('frontend.home', compact('categories', 'links', 'query', 'sort', 'direction'));
    }

    public function show(string $slug): View
    {
        $link = Link::query()
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->where('is_active', true)
            ->with('category')
            ->with('tags')
            ->firstOrFail();

        return view('frontend.link.show', compact('link'));
    }
}
