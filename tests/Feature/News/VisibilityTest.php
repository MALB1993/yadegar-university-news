<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('students do not see drafts in the news list', function () {
    Post::factory()->draft()->create(['title' => 'پیش‌نویس مخفی']);

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.index'))
        ->assertOk()
        ->assertDontSee('پیش‌نویس مخفی');
});

test('other users cannot open a draft', function () {
    $draft = Post::factory()->draft()->create();

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.show', $draft))
        ->assertForbidden();
});

test('a professor sees only their own drafts', function () {
    $professor = User::factory()->professor()->create();
    $otherProfessor = User::factory()->professor()->create();

    Post::factory()->draft()->create(['user_id' => $professor->id, 'title' => 'پیش‌نویس من']);
    Post::factory()->draft()->create(['user_id' => $otherProfessor->id, 'title' => 'پیش‌نویس استاد دیگر']);

    $this->actingAs($professor)
        ->get(route('news.index'))
        ->assertOk()
        ->assertSee('پیش‌نویس من')
        ->assertDontSee('پیش‌نویس استاد دیگر');
});

test('employees see every draft', function () {
    Post::factory()->draft()->create(['title' => 'پیش‌نویس همکار']);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('news.index'))
        ->assertOk()
        ->assertSee('پیش‌نویس همکار');
});

test('professors cannot pin news', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $this->actingAs($professor)
        ->post(route('news.store'), [
            'title' => 'خبر تستی استاد',
            'body' => 'متن خبری برای بررسی سنجاق کردن توسط استاد.',
            'category_id' => $category->id,
            'status' => 'published',
            'is_pinned' => 1,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('posts', ['title' => 'خبر تستی استاد', 'is_pinned' => false]);
});

test('employees can pin news', function () {
    $employee = User::factory()->employee()->create();
    $category = Category::factory()->create();

    $this->actingAs($employee)
        ->post(route('news.store'), [
            'title' => 'خبر مهم کارمند',
            'body' => 'متن خبری مهم که توسط کارمند سنجاق می‌شود.',
            'category_id' => $category->id,
            'status' => 'published',
            'is_pinned' => 1,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('posts', ['title' => 'خبر مهم کارمند', 'is_pinned' => true]);
});

test('pinned news is shown in the important section', function () {
    Post::factory()->pinned()->create(['title' => 'خبر سنجاق‌شده']);

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.index'))
        ->assertOk()
        ->assertSee('خبرهای مهم')
        ->assertSee('خبر سنجاق‌شده');
});

test('viewing a published post increases its views', function () {
    $post = Post::factory()->create(['views' => 0]);

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.show', $post))
        ->assertOk();

    expect($post->refresh()->views)->toBe(1);
});

test('the author viewing their own post does not count as a view', function () {
    $author = User::factory()->professor()->create();
    $post = Post::factory()->create(['user_id' => $author->id, 'views' => 0]);

    $this->actingAs($author)
        ->get(route('news.show', $post))
        ->assertOk();

    expect($post->refresh()->views)->toBe(0);
});

test('viewing a draft does not count as a view', function () {
    $draft = Post::factory()->draft()->create(['views' => 0]);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('news.show', $draft))
        ->assertOk();

    expect($draft->refresh()->views)->toBe(0);
});
