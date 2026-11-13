<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('professors cannot open the user management page', function () {
    $this->actingAs(User::factory()->professor()->create())
        ->get(route('users.index'))
        ->assertForbidden();
});

test('employees can see the user list', function () {
    User::factory()->student()->create(['fullname' => 'کاربر فهرست']);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('کاربر فهرست');
});

test('the user list can be filtered by role', function () {
    User::factory()->professor()->create(['fullname' => 'استاد فیلتر']);
    User::factory()->student()->create(['fullname' => 'دانشجوی فیلتر']);

    $this->actingAs(User::factory()->employee()->create())
        ->get(route('users.index', ['role' => 'professor']))
        ->assertOk()
        ->assertSee('استاد فیلتر')
        ->assertDontSee('دانشجوی فیلتر');
});

test('employees can change the role and status of another user', function () {
    $target = User::factory()->student()->create();

    $this->actingAs(User::factory()->employee()->create())
        ->put(route('users.update', $target), [
            'role' => 'professor',
            'status' => 'inactive',
        ])
        ->assertRedirect();

    $target->refresh();

    expect($target->role)->toBe('professor');
    expect($target->status)->toBe('inactive');
});

test('employees cannot change their own role', function () {
    $employee = User::factory()->employee()->create();

    $this->actingAs($employee)
        ->put(route('users.update', $employee), [
            'role' => 'student',
            'status' => 'active',
        ])
        ->assertForbidden();

    expect($employee->refresh()->role)->toBe('employee');
});

test('an unknown role is rejected', function () {
    $target = User::factory()->student()->create();

    $this->actingAs(User::factory()->employee()->create())
        ->put(route('users.update', $target), [
            'role' => 'admin',
            'status' => 'active',
        ])
        ->assertSessionHasErrors('role');

    expect($target->refresh()->role)->toBe('student');
});

test('professors cannot change other users', function () {
    $target = User::factory()->student()->create();

    $this->actingAs(User::factory()->professor()->create())
        ->put(route('users.update', $target), [
            'role' => 'professor',
            'status' => 'active',
        ])
        ->assertForbidden();

    expect($target->refresh()->role)->toBe('student');
});
