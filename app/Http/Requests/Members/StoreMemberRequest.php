<?php

namespace App\Http\Requests\Members;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->memberRules();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_born_again' => $this->boolean('is_born_again'),
            'is_baptized' => $this->boolean('is_baptized'),
            'holy_spirit_baptised' => $this->boolean('holy_spirit_baptised'),
        ]);
    }

    protected function memberRules(): array
    {
        return [
            'family_id' => ['nullable', 'integer', Rule::exists('families', 'id')],
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'tithe_code' => ['nullable', 'string', 'max:100'],
            'zone' => ['nullable', 'string', 'max:255'],
            'residency' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'string', 'max:100'],
            'profile_pic' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'partner_name' => ['nullable', 'string', 'max:255'],
            'married_date' => ['nullable', 'date'],
            'is_born_again' => ['nullable', 'boolean'],
            'born_again_date' => ['nullable', 'date'],
            'is_baptized' => ['nullable', 'boolean'],
            'baptized_date' => ['nullable', 'date'],
            'holy_spirit_baptised' => ['nullable', 'boolean'],
            'membership_date' => ['nullable', 'date'],
            'member_code' => ['nullable', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
