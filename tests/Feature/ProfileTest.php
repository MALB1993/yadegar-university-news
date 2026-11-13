<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('public');
});

test('a user can upload a profile picture', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'fullname' => $user->fullname,
            'email' => $user->email,
            'profile_picture' => UploadedFile::fake()->image('avatar.jpg'),
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->profile_picture)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_picture);
});

test('changing the password requires the current password', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'fullname' => $user->fullname,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');

    expect(Hash::check('password', $user->refresh()->password))->toBeTrue();
});

test('a wrong current password is rejected', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'fullname' => $user->fullname,
            'email' => $user->email,
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertSessionHasErrors('current_password');
});

test('a user can change the password with the correct current password', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'fullname' => $user->fullname,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(Hash::check('new-password-123', $user->refresh()->password))->toBeTrue();
});

test('a phone number already used by another user is rejected', function () {
    $owner = User::factory()->student()->create(['phone_number' => '09121112233']);
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'fullname' => $user->fullname,
            'email' => $user->email,
            'phone_number' => $owner->phone_number,
        ])
        ->assertSessionHasErrors('phone_number');
});

test('profile updates cannot change the role', function () {
    $user = User::factory()->student()->create();

    $this->actingAs($user)
        ->put(route('profile.update'), [
            'fullname' => 'نام جدید',
            'email' => $user->email,
            'role' => 'employee',
        ])
        ->assertRedirect();

    $user->refresh();

    expect($user->fullname)->toBe('نام جدید');
    expect($user->role)->toBe('student');
});
