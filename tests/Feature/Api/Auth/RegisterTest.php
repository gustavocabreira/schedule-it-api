<?php

declare(strict_types=1);

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
