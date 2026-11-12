<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('create', Comment::class);

        $comment = new Comment($request->validated());
        $comment->user()->associate($request->user());
        $post->comments()->save($comment);

        return redirect()
            ->route('news.show', ['post' => $post->id])
            ->withFragment('comments')
            ->with('status', 'نظر شما ثبت شد.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $postId = $comment->post_id;
        $comment->delete();

        return redirect()
            ->route('news.show', ['post' => $postId])
            ->withFragment('comments')
            ->with('status', 'نظر حذف شد.');
    }
}
