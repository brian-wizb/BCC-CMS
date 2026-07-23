<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'              => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'full_name'             => ['nullable', 'string', 'max:255'],
            'email'                 => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->withoutTrashed()],
            'leader_id'             => ['nullable', 'integer', 'exists:leaders,id', Rule::unique('leaders', 'user_id')],
            'role'                  => ['required', 'string', Rule::in(array_keys(config('permissions.roles', [])))],
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => ['required', 'string'],
                    'profile_photo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
