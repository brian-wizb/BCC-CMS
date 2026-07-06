<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupBulkMembersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_ids' => ['required', 'array', 'min:1'],
            'member_ids.*' => ['integer', Rule::exists('members', 'id')],
            'role' => ['required', 'string', Rule::in(['member', 'leader', 'coordinator'])],
            'joined_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
