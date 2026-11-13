<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Show the news list. On the first page without filters the pinned news is shown on top.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->string('q')->trim()->toString();
        $categoryId = $request->integer('category') ?: null;
        $followedOnly = $request->boolean('followed');
        $followedIds = $user->followedCategories()->pluck('categories.id');
        $isFiltered = $search !== '' || $categoryId !== null || $followedOnly;
        $showPinned = ! $isFiltered && $request->integer('page', 1) === 1;

        $pinnedPosts = $showPinned
            ? $this->baseQuery($user)->where('is_pinned', true)->latest()->take(3)->get()
            : collect();

        $posts = $this->baseQuery($user)
            ->when($search !== '', fn (Builder $query) => $query->where('title', 'like', '%'.addcslashes($search, '%_\\').'%'))
            ->when($categoryId, fn (Builder $query) => $query->where('category_id', $categoryId))
            ->when($followedOnly, fn (Builder $query) => $query->whereIn('category_id', $followedIds))
            ->when($showPinned, fn (Builder $query) => $query->where('is_pinned', false))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('news.index', [
            'posts' => $posts,
            'pinnedPosts' => $pinnedPosts,
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'search' => $search,
            'categoryId' => $categoryId,
            'followedOnly' => $followedOnly,
            'hasFollowedCategories' => $followedIds->isNotEmpty(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('news.create', [
            'post' => new Post,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $data = $request->safe()->except(['image', 'published_at']);
        $data['status'] ??= 'published';
        // Only employees can pin news.
        $data['is_pinned'] = $request->user()->isEmployee() && $request->boolean('is_pinned');
        $data['published_at'] = $this->scheduledAt($request, $data['status']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // The author always comes from the logged in user, never from the form.
        $post = $request->user()->posts()->create($data);

        return redirect()->route('news.show', $post)->with('status', $post->isScheduled()
            ? 'خبر ثبت و برای زمان مشخص‌شده زمان‌بندی شد.'
            : 'خبر با موفقیت ثبت شد.');
    }

    /**
     * Show one news item. Views are counted for live news read by someone other than the author.
     */
    public function show(Request $request, Post $post): View
    {
        $this->authorize('view', $post);

        $user = $request->user();

        if ($post->isLive() && (int) $post->user_id !== (int) $user->id) {
            $post->increment('views');
        }

        $this->rememberRecentlyRead($request, $post);

        $post->load([
            'user',
            'category',
            'comments' => fn ($query) => $query->with('user')->withCount('likedBy'),
        ]);

        $related = $this->baseQuery($user)
            ->where('category_id', $post->category_id)
            ->whereKeyNot($post->id)
            ->latest()
            ->take(3)
            ->get();

        return view('news.show', [
            'post' => $post,
            'related' => $related,
            'isBookmarked' => $user->bookmarkedPosts()->whereKey($post->id)->exists(),
            'isFollowingCategory' => $user->followedCategories()->whereKey($post->category_id)->exists(),
            'likedCommentIds' => $user->likedComments()->pluck('comments.id')->map(fn ($id) => (int) $id)->all(),
        ]);
    }

    /**
     * Copy a news item as a draft for the current user, so it can be reused and edited.
     */
    public function duplicate(Post $post, Request $request): RedirectResponse
    {
        $this->authorize('view', $post);
        $this->authorize('create', Post::class);

        $copy = $request->user()->posts()->create([
            'category_id' => $post->category_id,
            'title' => 'کپی از: '.Str::limit($post->title, 200, ''),
            'body' => $post->body,
            'status' => 'draft',
            'is_pinned' => false,
        ]);

        return redirect()
            ->route('news.edit', $copy)
            ->with('status', 'یک پیش‌نویس از این خبر ساخته شد. می‌توانید آن را ویرایش و منتشر کنید.');
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('news.edit', [
            'post' => $post,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $data = $request->safe()->except(['image', 'published_at']);
        $data['status'] ??= $post->status;
        $data['published_at'] = $this->scheduledAt($request, $data['status']);

        if ($request->user()->isEmployee()) {
            $data['is_pinned'] = $request->boolean('is_pinned');
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($post);
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->route('news.show', $post)->with('status', 'خبر ویرایش شد.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $this->deleteImage($post);
        $post->delete();

        return redirect()->route('news.index')->with('status', 'خبر حذف شد.');
    }

    /**
     * News the user is allowed to see, with the relations the cards need.
     */
    private function baseQuery(User $user): Builder
    {
        return Post::query()
            ->visibleTo($user)
            ->with(['user', 'category'])
            ->withCount('comments');
    }

    /**
     * The publish time from the form. Empty, or a draft, means no schedule.
     */
    private function scheduledAt(Request $request, string $status): ?Carbon
    {
        if ($status !== 'published' || ! $request->filled('published_at')) {
            return null;
        }

        return Carbon::parse($request->string('published_at')->toString());
    }

    /**
     * Keep the last five news items the user opened, newest first, in the session.
     */
    private function rememberRecentlyRead(Request $request, Post $post): void
    {
        $recentIds = collect($request->session()->get('recently_read', []))
            ->reject(fn ($id) => (int) $id === $post->id)
            ->prepend($post->id)
            ->take(5)
            ->values()
            ->all();

        $request->session()->put('recently_read', $recentIds);
    }

    private function deleteImage(Post $post): void
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
    }
}
