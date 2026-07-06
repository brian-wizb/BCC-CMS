<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && ! $this->filled('slug')) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }
    }

    public function rules(): array
    {
        return $this->groupRules();
    }

    protected function groupRules(?int $excludeId = null): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:200', Rule::unique('groups', 'slug')->ignore($excludeId)],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'max:30'],
        ];
    }
}
