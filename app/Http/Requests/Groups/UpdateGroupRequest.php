<?php

namespace App\Http\Requests\Groups;

class UpdateGroupRequest extends StoreGroupRequest
{
    public function rules(): array
    {
        return $this->groupRules(excludeId: $this->route('group')?->id);
    }
}
