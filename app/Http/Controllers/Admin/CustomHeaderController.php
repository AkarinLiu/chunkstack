<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomHeaderRequest;
use App\Models\CustomHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomHeaderController extends Controller
{
    public function index(): View
    {
        $headers = CustomHeader::query()
            ->orderBy('sort_order')
            ->get();

        return view('admin.custom-headers.index', compact('headers'));
    }

    public function create(): View
    {
        return view('admin.custom-headers.create');
    }

    public function store(CustomHeaderRequest $request): RedirectResponse
    {
        CustomHeader::create($request->validated());

        return redirect()->route('admin.custom-headers.index')
            ->with('success', 'Head 注入创建成功');
    }

    public function edit(CustomHeader $customHeader): View
    {
        return view('admin.custom-headers.edit', compact('customHeader'));
    }

    public function update(CustomHeaderRequest $request, CustomHeader $customHeader): RedirectResponse
    {
        $customHeader->update($request->validated());

        return redirect()->route('admin.custom-headers.index')
            ->with('success', 'Head 注入更新成功');
    }

    public function destroy(CustomHeader $customHeader): RedirectResponse
    {
        $customHeader->delete();

        return redirect()->route('admin.custom-headers.index')
            ->with('success', 'Head 注入删除成功');
    }
}
