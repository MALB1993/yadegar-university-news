<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Every signed in user can comment.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Employees can remove any comment, everybody else only their own.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->isEmployee() || (int) $comment->user_id === (int) $user->id;
    }
}
