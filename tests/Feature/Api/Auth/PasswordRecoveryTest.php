<?php

declare(strict_types=1);

use App\Models\PasswordRecoveryToken;
use App\Models\User;
use App\Notifications\PasswordRecoveryTokenNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

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

it('should send an email with a password recovery link', function () {
    Notification::fake();
    $user = User::factory()->create();
    $payload = [
        'email' => $user->email,
    ];

    $this->postJson(route('api.auth.password-recovery.generate-token'), $payload);

    Notification::assertSentTo($user, PasswordRecoveryTokenNotification::class, function ($notification) {
        return $notification->token !== null;
    });
});

it('should return a 404 if the token is not found', function () {
    $response = $this->getJson(route('api.auth.password-recovery.check-token', [
        'passwordRecoveryToken' => 'invalid-token',
    ]));

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

it('should return a 200 if the token is found', function () {
    $user = User::factory()->create();

    $id = $user->id;
    $email = $user->email;
    $now = now()->timestamp;

    $decryptedToken = sprintf('%s:%s:%s', $id, $email, $now);
    $encryptedToken = encrypt($decryptedToken);

    $passwordRecoveryToken = PasswordRecoveryToken::create([
        'user_id' => $user->id,
        'token' => $encryptedToken,
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->getJson(route('api.auth.password-recovery.check-token', [
        'passwordRecoveryToken' => $passwordRecoveryToken->token,
    ]));

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonFragment([
        'id' => $user->id,
        'email' => $user->email,
    ]);
});

it('should update the user password', function () {
    $user = User::factory()->create();

    $id = $user->id;
    $email = $user->email;
    $now = now()->timestamp;

    $decryptedToken = sprintf('%s:%s:%s', $id, $email, $now);
    $encryptedToken = encrypt($decryptedToken);

    $passwordRecoveryToken = PasswordRecoveryToken::create([
        'user_id' => $user->id,
        'token' => $encryptedToken,
        'expires_at' => now()->addMinutes(5),
    ]);

    $payload = [
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.password-recovery.update-password', [
        'passwordRecoveryToken' => $passwordRecoveryToken->token,
    ]), $payload);

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJson([
        'message' => 'Your password has been updated.',
    ]);

    $user = $user->fresh();

    expect(Hash::check('P@ssw0rd', $user->password))->toBeTrue();
});

it('should return 422 when providing weak password', function () {
    $user = User::factory()->create();

    $id = $user->id;
    $email = $user->email;
    $now = now()->timestamp;

    $decryptedToken = sprintf('%s:%s:%s', $id, $email, $now);
    $encryptedToken = encrypt($decryptedToken);

    $passwordRecoveryToken = PasswordRecoveryToken::create([
        'user_id' => $user->id,
        'token' => $encryptedToken,
        'expires_at' => now()->addMinutes(5),
    ]);

    $payload = [
        'password' => 'pass',
        'password_confirmation' => 'pass',
    ];

    $response = $this->postJson(route('api.auth.password-recovery.update-password', [
        'passwordRecoveryToken' => $passwordRecoveryToken->token,
    ]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJson([
        'errors' => [
            'password' => [
                'The password field must be at least 8 characters.',
                'The password field must contain at least one uppercase and one lowercase letter.',
                'The password field must contain at least one symbol.',
                'The password field must contain at least one number.',
            ],
        ],
    ]);
});
