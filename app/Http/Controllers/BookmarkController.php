<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * The news the user saved for later, most recently saved first.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $posts = $user->bookmarkedPosts()
            ->visibleTo($user)
            ->with(['user', 'category'])
            ->withCount('comments')
            ->orderByDesc('post_user.created_at')
            ->paginate(9)
            ->withQueryString();

        return view('news.saved', ['posts' => $posts]);
    }

    public function toggle(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('view', $post);

        $changes = $request->user()->bookmarkedPosts()->toggle($post->id);

        $message = $changes['attached'] !== []
            ? 'به نشان‌شده‌ها اضافه شد.'
            : 'از نشان‌شده‌ها حذف شد.';

        return back()->with('status', $message);
    }
}
