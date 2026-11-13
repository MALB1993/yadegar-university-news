<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('a student can follow and unfollow a category', function () {
    $student = User::factory()->student()->create();
    $category = Category::factory()->create();

    $this->actingAs($student)
        ->post(route('categories.follow', $category))
        ->assertRedirect();

    expect($student->followedCategories()->whereKey($category->id)->exists())->toBeTrue();

    $this->actingAs($student)
        ->post(route('categories.follow', $category))
        ->assertRedirect();

    expect($student->followedCategories()->whereKey($category->id)->exists())->toBeFalse();
});

test('the followed filter only shows news from followed categories', function () {
    $student = User::factory()->student()->create();
    $followed = Category::factory()->create();
    $other = Category::factory()->create();
    $student->followedCategories()->attach($followed);

    Post::factory()->create(['category_id' => $followed->id, 'title' => 'خبر دسته‌ی دنبال‌شده']);
    Post::factory()->create(['category_id' => $other->id, 'title' => 'خبر دسته‌ی دیگر']);

    $this->actingAs($student)
        ->get(route('news.index', ['followed' => 1]))
        ->assertOk()
        ->assertSee('خبر دسته‌ی دنبال‌شده')
        ->assertDontSee('خبر دسته‌ی دیگر');
});

test('the student dashboard shows the feed of followed categories', function () {
    $student = User::factory()->student()->create();
    $category = Category::factory()->create();
    $student->followedCategories()->attach($category);
    Post::factory()->create(['category_id' => $category->id, 'title' => 'خبر در فید']);

    $this->actingAs($student)
        ->get(route('client.index'))
        ->assertOk()
        ->assertSee('خبر در فید');
});

test('recently read news is listed on the student dashboard', function () {
    $student = User::factory()->student()->create();
    $first = Post::factory()->create(['title' => 'اولین خبر خوانده‌شده']);
    $second = Post::factory()->create(['title' => 'دومین خبر خوانده‌شده']);

    $this->actingAs($student)->get(route('news.show', $first))->assertOk();
    $this->actingAs($student)->get(route('news.show', $second))->assertOk();

    $this->actingAs($student)
        ->get(route('client.index'))
        ->assertOk()
        ->assertSee('اولین خبر خوانده‌شده')
        ->assertSee('دومین خبر خوانده‌شده');
});

test('a student can like and unlike a comment', function () {
    $student = User::factory()->student()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => User::factory()->student()->create()->id,
    ]);

    $this->actingAs($student)
        ->post(route('comments.like', $comment))
        ->assertRedirect();

    expect($student->likedComments()->whereKey($comment->id)->exists())->toBeTrue();

    $this->actingAs($student)
        ->post(route('comments.like', $comment))
        ->assertRedirect();

    expect($student->likedComments()->whereKey($comment->id)->exists())->toBeFalse();
});

test('a student cannot like a comment on news they cannot see', function () {
    $draft = Post::factory()->draft()->create();
    $comment = Comment::factory()->create([
        'post_id' => $draft->id,
        'user_id' => User::factory()->student()->create()->id,
    ]);

    $this->actingAs(User::factory()->student()->create())
        ->post(route('comments.like', $comment))
        ->assertForbidden();
});
