<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('a user can save and unsave a post', function () {
    $student = User::factory()->student()->create();
    $post = Post::factory()->create();

    $this->actingAs($student)
        ->post(route('news.bookmark', $post))
        ->assertRedirect();

    $this->assertDatabaseHas('post_user', ['post_id' => $post->id, 'user_id' => $student->id]);

    $this->actingAs($student)
        ->post(route('news.bookmark', $post))
        ->assertRedirect();

    $this->assertDatabaseMissing('post_user', ['post_id' => $post->id, 'user_id' => $student->id]);
});

test('saved posts are listed on the bookmarks page', function () {
    $student = User::factory()->student()->create();
    $post = Post::factory()->create(['title' => 'خبر ذخیره‌شده']);
    $student->bookmarkedPosts()->attach($post);

    $this->actingAs($student)
        ->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee('خبر ذخیره‌شده');
});

test('the bookmarks page is empty for a user with no saved posts', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee('هنوز خبری را ذخیره نکرده‌اید.');
});

test('a user cannot save a draft they are not allowed to see', function () {
    $draft = Post::factory()->draft()->create();

    $this->actingAs(User::factory()->student()->create())
        ->post(route('news.bookmark', $draft))
        ->assertForbidden();
});
