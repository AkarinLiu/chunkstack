<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name,' . $this->tag,
            'slug' => 'required|string|max:255|unique:tags,slug,' . $this->tag,
            'color' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '标签名称',
            'slug' => 'URL 别名',
            'color' => '颜色',
        ];
    }
}
