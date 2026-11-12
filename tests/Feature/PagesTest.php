<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('the welcome page is public and offers a login link', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('ورود');
});

test('the home page shows the latest news to signed in users', function () {
    Post::factory()->create(['title' => 'اعلام زمان کلاس']);

    $this->actingAs(User::factory()->student()->create())
        ->get('/home')
        ->assertOk()
        ->assertSee('اعلام زمان کلاس');
});

test('the student and staff dashboards list the latest news', function () {
    Post::factory()->create(['title' => 'جلسه‌ی شورا']);

    $this->actingAs(User::factory()->student()->create())
        ->get(route('client.index'))
        ->assertOk()
        ->assertSee('جلسه‌ی شورا');

    $this->actingAs(User::factory()->professor()->create())
        ->get(route('owner.index'))
        ->assertOk()
        ->assertSee('جلسه‌ی شورا');
});
