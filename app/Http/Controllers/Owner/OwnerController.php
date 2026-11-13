<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(Request $request): View
    {
        return view('owner.index', [
            'latestPosts' => Post::query()
                ->visibleTo($request->user())
                ->with(['user', 'category'])
                ->withCount('comments')
                ->latest()
                ->take(4)
                ->get(),
            'stats' => [
                'posts' => Post::count(),
                'drafts' => Post::where('status', 'draft')->count(),
                'scheduled' => Post::where('status', 'published')->where('published_at', '>', now())->count(),
                'users' => User::count(),
                'comments' => Comment::count(),
            ],
            'categoryReport' => Category::query()
                ->withCount('posts')
                ->withSum('posts', 'views')
                ->orderBy('name')
                ->get(),
            'isEmployee' => $request->user()->isEmployee(),
        ]);
    }
}
