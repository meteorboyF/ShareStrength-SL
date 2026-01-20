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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:pwd,caregiver'],
            'phone' => ['nullable', 'string', 'max:20'],
            'skills' => ['nullable', 'string'],
            'disability_type' => ['nullable', 'string'],
        ];

        if ($this->input('role') === 'caregiver') {
            $rules['email'][] = 'unique:helpers,email';
        } else {
            $rules['email'][] = 'unique:users,email';
        }

        return $rules;
    }
}
