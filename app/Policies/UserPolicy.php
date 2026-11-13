<?php

namespace App\Policies;

use App\Models\User;

/**
 * Only employees manage the accounts. Nobody can change their own role or status here.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployee();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isEmployee() && $user->id !== $target->id;
    }
}
