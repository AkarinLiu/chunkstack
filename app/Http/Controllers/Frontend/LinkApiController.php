<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkApiController extends Controller
{
    public function index(Request $request): JsonResponse
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
                ->with('category')
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

            return response()->json([
                'query' => $query,
                'links' => $links,
                'categories' => [],
            ]);
        }

        $categories = Category::query()
            ->with('activeLinks.tags')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function ($category) use ($sort, $direction) {
                $sorted = $category->activeLinks->sortBy([
                    [$sort, $direction === 'desc' ? false : true],
                ])->values();
                $category->setRelation('activeLinks', $sorted);
            });

        return response()->json([
            'query' => null,
            'links' => [],
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): JsonResponse
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
            ->first();

        if (! $link) {
            return response()->json(['message' => '链接不存在'], 404);
        }

        return response()->json([
            'link' => $link,
        ]);
    }

    public function view(string $slug): JsonResponse
    {
        $link = Link::query()
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->first();

        if (! $link) {
            return response()->json(['message' => '链接不存在'], 404);
        }

        $link->incrementPageViewCount();

        return response()->json(['success' => true]);
    }

    public function click(string $slug): JsonResponse
    {
        $link = Link::query()
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->first();

        if (! $link) {
            return response()->json(['message' => '链接不存在'], 404);
        }

        $link->incrementClickCount();

        return response()->json(['success' => true]);
    }
}
