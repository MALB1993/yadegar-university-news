<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProfessorStatsController extends Controller
{
    /**
     * Statistics for the news written by the signed in professor or employee.
     */
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->withCount('comments')
            ->latest()
            ->get();

        return view('owner.my-news', [
            'posts' => $posts,
            'topPosts' => $posts->sortByDesc('views')->take(5)->values(),
            'maxViews' => max(1, (int) $posts->max('views')),
            'stats' => [
                'total' => $posts->count(),
                'views' => (int) $posts->sum('views'),
                'comments' => (int) $posts->sum('comments_count'),
                'drafts' => $posts->where('status', 'draft')->count(),
                'scheduled' => $posts->filter(fn (Post $post) => $post->isScheduled())->count(),
            ],
        ]);
    }
}
