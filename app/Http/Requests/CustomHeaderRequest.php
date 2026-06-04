<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomHeaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '名称',
            'content' => 'HTML 内容',
            'is_active' => '是否启用',
            'sort_order' => '排序',
        ];
    }
}
