<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Link;
use App\Models\Tag;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'categories' => Category::count(),
            'links' => Link::count(),
            'tags' => Tag::count(),
            'total_page_views' => Link::sum('page_view_count'),
            'total_clicks' => Link::sum('click_count'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
