<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'categories' => \App\Models\Category::count(),
            'links' => \App\Models\Link::count(),
            'tags' => \App\Models\Tag::count(),
            'total_clicks' => \App\Models\Link::sum('click_count'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
