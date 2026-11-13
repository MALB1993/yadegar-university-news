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

test('employees can search comments by their text', function () {
    $post = Post::factory()->create();
    Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => User::factory()->student()->create()->id,
        'body' => 'متن قابل جستجو',
    ]);
    Comment::factory()->create([
        'post_id' => $post->id,
        'user_id' => User::factory()->student()->create()->id,
        'body' => 'متن دیگر',
    ]);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('moderation.comments', ['q' => 'قابل جستجو']))
        ->assertOk()
        ->assertSee('متن قابل جستجو')
        ->assertDontSee('متن دیگر');
});

test('professors cannot open comment moderation', function () {
    $this->actingAs(User::factory()->professor()->create())
        ->get(route('moderation.comments'))
        ->assertForbidden();
});

test('employees can delete any comment from the moderation page', function () {
    $comment = Comment::factory()->create([
        'post_id' => Post::factory()->create()->id,
        'user_id' => User::factory()->student()->create()->id,
    ]);

    $this->actingAs(User::factory()->employee()->create())
        ->delete(route('comments.destroy', $comment))
        ->assertRedirect();

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('the employee dashboard shows the total views of each category', function () {
    $category = Category::factory()->create(['name' => 'گزارش تستی']);
    Post::factory()->create(['category_id' => $category->id, 'views' => 42]);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('owner.index'))
        ->assertOk()
        ->assertSee('گزارش تستی')
        ->assertSee('42');
});
