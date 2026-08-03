<?php

namespace App\Rules;

use App\Models\Group;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserIsMemberOfGroup implements Rule
{
    protected User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
    }

    public function passes($attribute, $value): bool
    {
        // If group_id is null, it's optional
        if ($value === null) {
            return true;
        }

        // If no user, fail
        if (!$this->user) {
            return false;
        }

        // Check if user is a member of the group
        $group = Group::find($value);
        
        return $group && $group->membres->contains($this->user->id);
    }

    public function message(): string
    {
        return 'Vous devez être membre du groupe pour y poster.';
    }
}
