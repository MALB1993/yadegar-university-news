<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('the my news page lists only the signed in professor news', function () {
    $professor = User::factory()->professor()->create();
    Post::factory()->create(['user_id' => $professor->id, 'title' => 'خبر من']);
    Post::factory()->create(['title' => 'خبر استاد دیگر']);

    $this->actingAs($professor)
        ->get(route('owner.my-news'))
        ->assertOk()
        ->assertSee('خبر من')
        ->assertDontSee('خبر استاد دیگر');
});

test('students cannot open the my news page', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get(route('owner.my-news'))
        ->assertForbidden();
});

test('a professor can duplicate news as a draft of their own', function () {
    $copier = User::factory()->professor()->create();
    Post::factory()->create(['title' => 'خبر اصلی']);

    $this->actingAs($copier)
        ->post(route('news.duplicate', Post::where('title', 'خبر اصلی')->firstOrFail()))
        ->assertRedirect();

    $copy = Post::where('title', 'کپی از: خبر اصلی')->firstOrFail();

    expect($copy->user_id)->toBe($copier->id);
    expect($copy->status)->toBe('draft');
    expect($copy->is_pinned)->toBeFalse();
});

test('students cannot duplicate news', function () {
    $post = Post::factory()->create();

    $this->actingAs(User::factory()->student()->create())
        ->post(route('news.duplicate', $post))
        ->assertForbidden();
});
