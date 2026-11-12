<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('public');
});

test('guests cannot open the news section', function () {
    $this->get('/news')->assertRedirect('/login');
});

test('any signed in user can read the news', function () {
    $post = Post::factory()->create(['title' => 'خبر تست']);

    $this->actingAs(User::factory()->student()->create())
        ->get('/news')
        ->assertOk()
        ->assertSee('خبر تست');

    $this->actingAs(User::factory()->student()->create())
        ->get(route('news.show', $post))
        ->assertOk();
});

test('students cannot publish news', function () {
    $this->actingAs(User::factory()->student()->create())
        ->get('/news/create')
        ->assertForbidden();
});

test('a professor can publish news with an image', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($professor)->post('/news', [
        'title' => 'اطلاعیه امتحانات',
        'body' => 'امتحانات پایان ترم از هفته‌ی آینده برگزار می‌شود.',
        'category_id' => $category->id,
        'image' => UploadedFile::fake()->image('poster.jpg'),
    ]);

    $post = Post::where('title', 'اطلاعیه امتحانات')->firstOrFail();

    $response->assertRedirect(route('news.show', $post));
    expect($post->user_id)->toBe($professor->id);
    Storage::disk('public')->assertExists($post->image);
});

test('publishing validates the title and the image type', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $this->actingAs($professor)
        ->from('/news/create')
        ->post('/news', [
            'title' => '',
            'body' => 'متن کافی برای خبر',
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->create('script.php', 10),
        ])
        ->assertSessionHasErrors(['title', 'image']);

    expect(Post::count())->toBe(0);
});

test('an image bigger than 2 MB is rejected', function () {
    $professor = User::factory()->professor()->create();
    $category = Category::factory()->create();

    $this->actingAs($professor)
        ->from('/news/create')
        ->post('/news', [
            'title' => 'خبر بزرگ',
            'body' => 'متن کافی برای خبر',
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('huge.jpg')->size(3000),
        ])
        ->assertSessionHasErrors('image');
});

test('professors can only edit their own news', function () {
    $owner = User::factory()->professor()->create();
    $otherProfessor = User::factory()->professor()->create();
    $post = Post::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($otherProfessor)->get(route('news.edit', $post))->assertForbidden();
    $this->actingAs($owner)->get(route('news.edit', $post))->assertOk();
});

test('employees can edit and delete any news', function () {
    $employee = User::factory()->employee()->create();
    $post = Post::factory()->create();

    $this->actingAs($employee)->get(route('news.edit', $post))->assertOk();
    $this->actingAs($employee)->delete(route('news.destroy', $post))->assertRedirect(route('news.index'));

    expect(Post::whereKey($post->id)->exists())->toBeFalse();
});

test('replacing the image removes the old file', function () {
    $professor = User::factory()->professor()->create();
    $oldPath = 'posts/old-image.jpg';
    Storage::disk('public')->put($oldPath, 'old');
    $post = Post::factory()->create(['user_id' => $professor->id, 'image' => $oldPath]);

    $this->actingAs($professor)
        ->put(route('news.update', $post), [
            'title' => $post->title,
            'body' => 'متن جدید برای خبر',
            'category_id' => $post->category_id,
            'image' => UploadedFile::fake()->image('new.png'),
        ])
        ->assertRedirect(route('news.show', $post));

    $newImage = $post->fresh()->image;

    Storage::disk('public')->assertMissing($oldPath);
    expect($newImage)->not->toBe($oldPath);
    Storage::disk('public')->assertExists($newImage);
});

test('deleting news also removes its image', function () {
    $professor = User::factory()->professor()->create();
    Storage::disk('public')->put('posts/to-delete.jpg', 'x');
    $post = Post::factory()->create(['user_id' => $professor->id, 'image' => 'posts/to-delete.jpg']);

    $this->actingAs($professor)->delete(route('news.destroy', $post));

    Storage::disk('public')->assertMissing('posts/to-delete.jpg');
    expect(Post::whereKey($post->id)->exists())->toBeFalse();
});

test('news can be searched by title', function () {
    Post::factory()->create(['title' => 'برنامه‌ی هفته']);
    Post::factory()->create(['title' => 'اطلاعیه‌ی کلاس']);

    $this->actingAs(User::factory()->student()->create())
        ->get('/news?q='.urlencode('برنامه'))
        ->assertSee('برنامه‌ی هفته')
        ->assertDontSee('اطلاعیه‌ی کلاس');
});
