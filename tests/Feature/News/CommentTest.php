<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('guests cannot comment', function () {
    $post = Post::factory()->create();

    $this->post(route('comments.store', $post), ['body' => 'نظر تست'])
        ->assertRedirect('/login');

    expect(Comment::count())->toBe(0);
});

test('a signed in user comment is saved with the author and shown right away', function () {
    $student = User::factory()->student()->create();
    $post = Post::factory()->create();

    $this->actingAs($student)
        ->post(route('comments.store', $post), ['body' => 'ممنون از خبر'])
        ->assertRedirect(route('news.show', $post).'#comments');

    $comment = Comment::firstOrFail();

    expect($comment->user_id)->toBe($student->id);
    expect($comment->post_id)->toBe($post->id);

    $this->actingAs($student)
        ->get(route('news.show', $post))
        ->assertSee('ممنون از خبر');
});

test('comments are escaped so scripts cannot be injected', function () {
    $student = User::factory()->student()->create();
    $post = Post::factory()->create();

    $this->actingAs($student)->post(route('comments.store', $post), [
        'body' => '<script>alert("x")</script>',
    ]);

    $this->actingAs($student)
        ->get(route('news.show', $post))
        ->assertSee('&lt;script&gt;', false)
        ->assertDontSee('<script>alert', false);
});

test('empty comments are rejected', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->student()->create())
        ->from(route('news.show', $post))
        ->post(route('comments.store', $post), ['body' => ' '])
        ->assertSessionHasErrors('body');

    expect(Comment::count())->toBe(0);
});

test('users can delete their own comment but not someone else\'s', function () {
    $post = Post::factory()->create();
    $author = User::factory()->student()->create();
    $otherStudent = User::factory()->student()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $author->id]);

    $this->actingAs($otherStudent)->delete(route('comments.destroy', $comment))->assertForbidden();
    $this->actingAs($author)->delete(route('comments.destroy', $comment))->assertRedirect();

    expect(Comment::whereKey($comment->id)->exists())->toBeFalse();
});

test('employees can delete any comment', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create(['post_id' => $post->id]);

    $this->actingAs(User::factory()->employee()->create())
        ->delete(route('comments.destroy', $comment))
        ->assertRedirect();

    expect(Comment::whereKey($comment->id)->exists())->toBeFalse();
});
