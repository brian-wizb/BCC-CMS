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
        $firstName = trim((string) $this->input('first_name'));
        $middleName = trim((string) $this->input('middle_name'));
        $surname = trim((string) $this->input('surname'));

        $this->merge([
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'surname' => $surname,
            'full_name' => collect([$firstName, $middleName, $surname])
                ->filter(fn (string $part) => $part !== '')
                ->implode(' '),
            'partner_name' => trim((string) $this->input('partner_name')),
            'tithe_code' => trim((string) $this->input('tithe_code')),
            'phone' => trim((string) $this->input('phone')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'member_code' => trim((string) $this->input('member_code')),
            'is_born_again' => $this->boolean('is_born_again'),
            'is_baptized' => $this->boolean('is_baptized'),
            'holy_spirit_baptised' => $this->boolean('holy_spirit_baptised'),
            'share_partner_tithe_code' => $this->boolean('share_partner_tithe_code'),
            'is_university_student' => $this->boolean('is_university_student'),
        ]);
    }

    protected function memberRules(): array
    {
        $currentMemberId = optional($this->route('member'))->id;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50', Rule::unique('members', 'phone')->ignore($currentMemberId)],
            'tithe_code' => ['nullable', 'string', 'max:100'],
            'zone' => ['nullable', 'string', 'max:255', Rule::exists('zones', 'name')],
            'residency' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'string', Rule::in(['Single', 'Married', 'Divorced', 'Widowed', 'Separated', 'Unknown'])],
            'profile_pic' => ['nullable', 'string', 'max:255'],
            'profile_pic_file' => ['nullable', 'image', 'max:4096'],
            'date_of_birth' => ['nullable', 'date'],
            'partner_member_id' => ['nullable', 'integer', Rule::exists('members', 'id')],
            'partner_name' => ['nullable', 'string', 'max:255'],
            'married_date' => ['nullable', 'date'],
            'share_partner_tithe_code' => ['nullable', 'boolean'],
            'is_born_again' => ['nullable', 'boolean'],
            'born_again_date' => ['nullable', 'date'],
            'is_baptized' => ['nullable', 'boolean'],
            'baptized_date' => ['nullable', 'date'],
            'holy_spirit_baptised' => ['nullable', 'boolean'],
            'membership_date' => ['nullable', 'date'],
            'member_code' => ['nullable', 'string', 'max:100', Rule::unique('members', 'member_code')->ignore($currentMemberId)],
            'username' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('members', 'email')->ignore($currentMemberId)],
            'remarks' => ['nullable', 'string'],
            // Education & employment
            'employment_status' => ['nullable', 'string', Rule::in(['Employed', 'Unemployed', 'Entrepreneur', 'Self-employed', 'Student', 'Retired', 'Other'])],
            'is_university_student' => ['nullable', 'boolean'],
            'university_id' => ['nullable', 'integer', Rule::exists('universities', 'id')],
            'university_start_date' => ['nullable', 'date'],
            'university_end_date' => ['nullable', 'date', 'after_or_equal:university_start_date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $isMarried = $this->input('marital_status') === 'Married';
            $partnerMemberId = $this->input('partner_member_id');
            $partnerName = trim((string) $this->input('partner_name'));
            $marriedDate = $this->input('married_date');
            $sharePartnerTitheCode = $this->boolean('share_partner_tithe_code');
            $currentMemberId = optional($this->route('member'))->id;

            if ($this->filled('date_of_birth')) {
                $duplicateMember = \App\Models\Member::query()
                    ->when($currentMemberId, fn ($query) => $query->whereKeyNot($currentMemberId))
                    ->whereRaw('LOWER(TRIM(full_name)) = ?', [strtolower(trim((string) $this->input('full_name')))])
                    ->whereDate('date_of_birth', $this->input('date_of_birth'))
                    ->exists();

                if ($duplicateMember) {
                    $validator->errors()->add('date_of_birth', 'A member with the same name and date of birth already exists.');
                }
            }

            if (! $isMarried) {
                return;
            }

            if (blank($marriedDate)) {
                $validator->errors()->add('married_date', 'Marriage date is required when marital status is married.');
            }

            if (blank($partnerMemberId) && $partnerName === '') {
                $validator->errors()->add('partner_member_id', 'Select a registered partner or enter a partner name.');
            }

            if ($sharePartnerTitheCode && blank($partnerMemberId)) {
                $validator->errors()->add('share_partner_tithe_code', 'Select a registered partner to share tithe code.');
            }

            if ($currentMemberId !== null && (int) $partnerMemberId === (int) $currentMemberId) {
                $validator->errors()->add('partner_member_id', 'A member cannot be their own partner.');
            }
        });
    }
}
