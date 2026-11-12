<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('the login form has a single identification code input', function () {
    $response = $this->get('/login');

    $response->assertOk();

    expect(substr_count($response->getContent(), 'name="identification_code"'))->toBe(1);
});

test('a user can log in with the identification code', function () {
    $user = User::factory()->create(['identification_code' => '40123456']);

    $response = $this->post('/login', [
        'identification_code' => '40123456',
        'password' => 'password',
    ]);

    $response->assertRedirect('/home');
    $this->assertAuthenticatedAs($user);
});

test('login fails with a wrong password', function () {
    User::factory()->create(['identification_code' => '40123456']);

    $response = $this->from('/login')->post('/login', [
        'identification_code' => '40123456',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('identification_code');
    $this->assertGuest();
});

test('login fails for an unknown identification code', function () {
    $response = $this->from('/login')->post('/login', [
        'identification_code' => '99999999',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('identification_code');
    $this->assertGuest();
});

test('an inactive user cannot log in', function () {
    User::factory()->inactive()->create(['identification_code' => '40123456']);

    $response = $this->from('/login')->post('/login', [
        'identification_code' => '40123456',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('identification_code');
    $this->assertGuest();
});

test('the email address cannot be used to log in', function () {
    User::factory()->create(['email' => 'someone@example.com']);

    $response = $this->from('/login')->post('/login', [
        'email' => 'someone@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('identification_code');
    $this->assertGuest();
});
