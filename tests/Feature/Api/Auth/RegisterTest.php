<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;

it('should be able to register a user', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJson([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseCount('users', 1);
});

it('should return a 422 error when the password is not confirmed', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd2',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJson([
        'errors' => [
            'password' => [
                'The password field confirmation does not match.',
            ],
        ],
    ]);
});

it('should return email already exists error', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJson([
        'errors' => [
            'email' => [
                'The email has already been taken.',
            ],
        ],
    ]);

    $this->assertDatabaseCount('users', 1);
});
