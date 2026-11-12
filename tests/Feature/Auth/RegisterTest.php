<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function registrationData(array $overrides = []): array
{
    return array_merge([
        'fullname' => 'علی رضایی',
        'identification_code' => '40999999',
        'email' => 'new-user@example.com',
        'password' => 'a-strong-password',
        'password_confirmation' => 'a-strong-password',
    ], $overrides);
}

test('the registration form asks for an identification code', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('name="identification_code"', false);
});

test('a visitor can register as a student', function () {
    $response = $this->post('/register', registrationData());

    $user = User::where('identification_code', '40999999')->first();

    $response->assertRedirect('/home');
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('student');
    expect($user->status)->toBe('active');
    $this->assertAuthenticatedAs($user);
});

test('the role cannot be chosen during registration', function () {
    $this->post('/register', registrationData([
        'role' => 'professor',
        'status' => 'inactive',
    ]));

    $user = User::where('identification_code', '40999999')->firstOrFail();

    expect($user->role)->toBe('student');
    expect($user->status)->toBe('active');
});

test('the password is stored hashed', function () {
    $this->post('/register', registrationData());

    $user = User::where('identification_code', '40999999')->firstOrFail();

    expect($user->password)->not->toBe('a-strong-password');
    expect(password_verify('a-strong-password', $user->password))->toBeTrue();
});

test('the identification code is required', function () {
    $response = $this->post('/register', registrationData(['identification_code' => '']));

    $response->assertSessionHasErrors('identification_code');
    expect(User::count())->toBe(0);
});

test('the identification code must be unique', function () {
    User::factory()->create(['identification_code' => '40999999']);

    $response = $this->post('/register', registrationData());

    $response->assertSessionHasErrors('identification_code');
    expect(User::count())->toBe(1);
});

test('the identification code only accepts safe characters', function () {
    $response = $this->post('/register', registrationData(['identification_code' => '<script>alert(1)</script>']));

    $response->assertSessionHasErrors('identification_code');
    expect(User::count())->toBe(0);
});

test('the password confirmation must match', function () {
    $response = $this->post('/register', registrationData(['password_confirmation' => 'something-else']));

    $response->assertSessionHasErrors('password');
    expect(User::count())->toBe(0);
});
