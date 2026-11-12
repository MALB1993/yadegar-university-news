<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

/**
 * Only employees manage the categories.
 */
class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployee();
    }

    public function create(User $user): bool
    {
        return $user->isEmployee();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->isEmployee();
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isEmployee();
    }
}
