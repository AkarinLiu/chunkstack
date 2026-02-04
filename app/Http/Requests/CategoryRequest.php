<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $this->category,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '分类名称',
            'slug' => 'URL 别名',
            'description' => '描述',
            'sort_order' => '排序',
            'icon' => '图标',
            'is_active' => '是否启用',
        ];
    }
}
