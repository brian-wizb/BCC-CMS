<?php

namespace App\Http\Requests\Families;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFamilyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'head_of_family'  => ['required', 'string', 'max:255'],
            'gender'          => ['required', 'string', 'max:50'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'zone'            => ['nullable', 'string', 'max:255', Rule::exists('zones', 'name')],
            'address'         => ['nullable', 'string', 'max:500'],
            'home_cell_group' => ['nullable', 'string', 'max:255'],
            'joined_date'     => ['nullable', 'date'],
            'remarks'         => ['nullable', 'string'],
            'guest_members'   => ['nullable', 'array'],
            'guest_members.*' => ['nullable', 'string', 'max:255'],
            'member_ids'      => ['nullable', 'array'],
            'member_ids.*'    => ['nullable', 'integer', 'exists:members,id'],
        ];
    }
}
