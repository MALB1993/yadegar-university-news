<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Drafts and scheduled news are only visible to staff and to their author.
     */
    public function view(User $user, Post $post): bool
    {
        return $post->isLive()
            || $user->isEmployee()
            || (int) $post->user_id === (int) $user->id;
    }

    /**
     * Professors and employees can publish news.
     */
    public function create(User $user): bool
    {
        return $user->isProfessor() || $user->isEmployee();
    }

    /**
     * Employees can edit every post, professors only their own.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->isEmployee()
            || ($user->isProfessor() && (int) $post->user_id === (int) $user->id);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->update($user, $post);
    }
}
