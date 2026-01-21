<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountType = $this->input('account_type', 'pwd');
        $emailUniqueRule = $accountType === 'helpmate'
            ? 'unique:helpers,email'
            : 'unique:users,email';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'in:pwd,helpmate'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'skills' => ['nullable', 'string'],
            'disability_type' => ['nullable', 'string'],
        ];

        $rules['email'][] = $emailUniqueRule;

        return $rules;
    }
}
