<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_id'    => ['nullable', 'integer', Rule::exists('members', 'id')],
            'guest_name'   => ['nullable', 'string', 'max:255'],
            'guest_phone'  => ['nullable', 'string', 'max:50'],
            'role'         => ['required', 'string', Rule::in(['member', 'leader', 'coordinator'])],
            'joined_at'    => ['nullable', 'date'],
            'notes'        => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (blank($this->input('member_id')) && blank($this->input('guest_name'))) {
                $validator->errors()->add('member_id', 'Select a registered member or enter a guest name.');
            }
        });
    }
}
