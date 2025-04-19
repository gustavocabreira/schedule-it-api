<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;

it('should be able to login a user', function () {
    $user = User::factory()->create();

    $payload = [
        'email' => $user->email,
        'password' => 'password',
    ];

    $response = $this->postJson(route('api.auth.login'), $payload);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonStructure([
        'access_token',
    ]);
});

it('should return a 401 error when the credentials are invalid', function () {
    $payload = [
        'email' => 'not valid',
        'password' => 'password',
    ];

    $response = $this->postJson(route('api.auth.login'), $payload);

    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    $response->assertJson([
        'message' => 'Invalid credentials',
    ]);
});
