<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $followedIds = $user->followedCategories()->pluck('categories.id')->map(fn ($id) => (int) $id);

        return view('client.index', [
            'feedPosts' => $followedIds->isEmpty()
                ? collect()
                : $this->newsQuery($request)
                    ->whereIn('category_id', $followedIds)
                    ->latest()
                    ->take(6)
                    ->get(),
            'latestPosts' => $this->newsQuery($request)->latest()->take(3)->get(),
            'recentPosts' => $this->recentlyRead($request),
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'followedIds' => $followedIds,
        ]);
    }

    private function newsQuery(Request $request)
    {
        return Post::query()
            ->visibleTo($request->user())
            ->with(['user', 'category'])
            ->withCount('comments');
    }

    /**
     * The news the user opened recently, in the order they were opened.
     */
    private function recentlyRead(Request $request): Collection
    {
        $ids = collect($request->session()->get('recently_read', []))
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($ids === []) {
            return collect();
        }

        return $this->newsQuery($request)
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Post $post) => array_search($post->id, $ids, true))
            ->values();
    }
}
