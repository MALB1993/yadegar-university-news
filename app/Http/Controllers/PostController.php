<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Show the news list, optionally filtered by title and category.
     */
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $categoryId = $request->integer('category') ?: null;

        $posts = Post::query()
            ->with(['user', 'category'])
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.addcslashes($search, '%_\\').'%'))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('news.index', [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('news.create', [
            'post' => new Post(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // The author always comes from the logged in user, never from the form.
        $post = $request->user()->posts()->create($data);

        return redirect()->route('news.show', $post)->with('status', 'خبر با موفقیت ثبت شد.');
    }

    public function show(Post $post): View
    {
        $post->load(['user', 'category', 'comments.user']);

        return view('news.show', ['post' => $post]);
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

        $data = $request->safe()->except('image');

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

    private function deleteImage(Post $post): void
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
    }
}
