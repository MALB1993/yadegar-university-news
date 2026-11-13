<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('scheduled news is hidden from the news list of students', function () {
    Post::factory()->create([
        'title' => 'خبر زمان‌بندی‌شده‌ی مخفی',
        'published_at' => now()->addDay(),
    ]);

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.index'))
        ->assertOk()
        ->assertDontSee('خبر زمان‌بندی‌شده‌ی مخفی');
});

test('students cannot open scheduled news', function () {
    $post = Post::factory()->create(['published_at' => now()->addDay()]);

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.show', $post))
        ->assertForbidden();
});

test('employees can see scheduled news', function () {
    $post = Post::factory()->create([
        'title' => 'خبر آینده برای کارمند',
        'published_at' => now()->addDay(),
    ]);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('news.show', $post))
        ->assertOk()
        ->assertSee('خبر آینده برای کارمند');
});

test('scheduled news does not count as a view', function () {
    $post = Post::factory()->create(['published_at' => now()->addDay(), 'views' => 0]);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('news.show', $post))
        ->assertOk();

    expect($post->refresh()->views)->toBe(0);
});

test('a professor can schedule news for a future date', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $this->actingAs($professor)
        ->post(route('news.store'), [
            'title' => 'خبر زمان‌دار آینده',
            'body' => 'این خبر دو روز دیگر منتشر می‌شود.',
            'category_id' => $category->id,
            'status' => 'published',
            'published_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ])
        ->assertRedirect();

    $post = Post::where('title', 'خبر زمان‌دار آینده')->firstOrFail();

    expect($post->published_at->isFuture())->toBeTrue();
    expect($post->isScheduled())->toBeTrue();
});

test('an empty publish time publishes the news immediately', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $this->actingAs($professor)
        ->post(route('news.store'), [
            'title' => 'خبر فوری',
            'body' => 'این خبر همین حالا منتشر می‌شود.',
            'category_id' => $category->id,
            'status' => 'published',
            'published_at' => '',
        ])
        ->assertRedirect();

    expect(Post::where('title', 'خبر فوری')->firstOrFail()->published_at)->toBeNull();
});

test('drafts ignore the publish time', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $this->actingAs($professor)
        ->post(route('news.store'), [
            'title' => 'پیش‌نویس با زمان',
            'body' => 'این پیش‌نویس زمان‌بندی نمی‌شود.',
            'category_id' => $category->id,
            'status' => 'draft',
            'published_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
        ])
        ->assertRedirect();

    $post = Post::where('title', 'پیش‌نویس با زمان')->firstOrFail();

    expect($post->status)->toBe('draft');
    expect($post->published_at)->toBeNull();
});
