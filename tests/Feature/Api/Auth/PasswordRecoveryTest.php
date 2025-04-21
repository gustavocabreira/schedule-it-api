<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;

it('should generate a password recovery token', function () {
    $user = User::factory()->create();
    $payload = [
        'email' => $user->email,
    ];

    $response = $this->postJson(route('api.auth.password-recovery.generate-token'), $payload);
    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas('password_recovery_tokens', [
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseCount('password_recovery_tokens', 1);
});

it('should not generate a password recovery token if the email is not found', function () {
    $payload = [
        'email' => 'invalid@email.com',
    ];

    $response = $this->postJson(route('api.auth.password-recovery.generate-token'), $payload);
    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseCount('password_recovery_tokens', 0);
});
