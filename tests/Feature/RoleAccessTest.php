<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('guests are redirected to the login page', function (string $path) {
    $this->get($path)->assertRedirect('/login');
})->with(['/student', '/employee', '/home']);

test('students can only open the student area', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)->get('/student')->assertOk();
    $this->actingAs($student)->get('/employee')->assertForbidden();
});

test('professors and employees can only open the employee area', function (string $role) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->get('/employee')->assertOk();
    $this->actingAs($user)->get('/student')->assertForbidden();
})->with(['professor', 'employee']);

test('the home page links to the area of the signed in role', function (string $role, string $routeName) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)
        ->get('/home')
        ->assertOk()
        ->assertSee($user->fullname)
        ->assertSee(route($routeName), false);
})->with([
    'student' => ['student', 'client.index'],
    'professor' => ['professor', 'owner.index'],
    'employee' => ['employee', 'owner.index'],
]);

test('the navigation shows the full name of the signed in user', function () {
    $user = User::factory()->create(['fullname' => 'مریم احمدی']);

    $this->actingAs($user)
        ->get('/home')
        ->assertOk()
        ->assertSee('مریم احمدی');
});
