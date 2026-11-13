<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommentModerationController extends Controller
{
    /**
     * List all comments so employees can find and remove inappropriate ones.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $term = '%'.addcslashes($search, '%_\\').'%';

        $comments = Comment::query()
            ->with(['user', 'post'])
            ->withCount('likedBy')
            ->when($search !== '', function (Builder $query) use ($term): void {
                $query->where(function (Builder $query) use ($term): void {
                    $query->where('body', 'like', $term)
                        ->orWhereHas('user', fn (Builder $user) => $user->where('fullname', 'like', $term));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('comments.moderation', compact('comments', 'search'));
    }
}
