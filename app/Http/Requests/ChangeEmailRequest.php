<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique(User::class, 'email')->ignore(auth()->id()),
                function ($attribute, $value, $fail) {
                    if ($value === auth()->user()->email) {
                        $fail('新邮箱地址不能与当前邮箱地址相同');
                    }
                },
            ],
            'current_password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => '新邮箱地址',
            'current_password' => '当前密码',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! \Illuminate\Support\Facades\Hash::check($this->current_password, auth()->user()->password)) {
                $validator->errors()->add('current_password', '当前密码不正确');
            }
        });
    }
}
