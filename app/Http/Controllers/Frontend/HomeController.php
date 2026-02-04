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
                ->get();
            $categories = collect();
        } else {
            $categories = Category::query()
                ->with('activeLinks.tags')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            $links = collect();
        }

        return view('frontend.home', compact('categories', 'links', 'query'));
    }

    public function click(Link $link)
    {
        $link->incrementClickCount();

        return redirect($link->url);
    }
}
