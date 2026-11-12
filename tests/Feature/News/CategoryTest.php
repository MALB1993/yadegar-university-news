<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('only employees can manage categories', function () {
    $this->actingAs(User::factory()->professor()->create())
        ->post('/categories', ['name' => 'تحصیلات تکمیلی'])
        ->assertForbidden();

    $this->actingAs(User::factory()->student()->create())
        ->get('/categories')
        ->assertForbidden();

    expect(Category::count())->toBe(0);
});

test('an employee can create a category', function () {
    $this->actingAs(User::factory()->employee()->create())
        ->post('/categories', ['name' => 'تحصیلات تکمیلی'])
        ->assertRedirect(route('categories.index'));

    expect(Category::where('name', 'تحصیلات تکمیلی')->exists())->toBeTrue();
});

test('category names must be unique', function () {
    Category::factory()->create(['name' => 'آموزشی']);

    $this->actingAs(User::factory()->employee()->create())
        ->from('/categories')
        ->post('/categories', ['name' => 'آموزشی'])
        ->assertSessionHasErrors('name');

    expect(Category::count())->toBe(1);
});

test('an employee can rename a category to its own name', function () {
    $category = Category::factory()->create(['name' => 'فرهنگی']);

    $this->actingAs(User::factory()->employee()->create())
        ->put(route('categories.update', $category), ['name' => 'فرهنگی'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('categories.index'));
});

test('a category that still has news cannot be deleted', function () {
    $post = Post::factory()->create();
    $category = $post->category;

    $this->actingAs(User::factory()->employee()->create())
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'))
        ->assertSessionHas('error');

    expect(Category::whereKey($category->id)->exists())->toBeTrue();
});

test('an empty category can be deleted', function () {
    $category = Category::factory()->create();

    $this->actingAs(User::factory()->employee()->create())
        ->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'));

    expect(Category::whereKey($category->id)->exists())->toBeFalse();
});
