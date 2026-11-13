<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the dashboard for the signed in user.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('home', [
            'latestPosts' => Post::query()
                ->visibleTo($user)
                ->with(['user', 'category'])
                ->withCount('comments')
                ->latest()
                ->take(4)
                ->get(),
            'bookmarkCount' => $user->bookmarkedPosts()->count(),
            'commentCount' => $user->comments()->count(),
        ]);
    }
}
