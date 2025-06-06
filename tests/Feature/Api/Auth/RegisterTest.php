<?php

declare(strict_types=1);

use App\Actions\Notifications\WelcomeNotificationAction;
use App\Jobs\SendWelcomeNotificationJob;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
});

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

it('should return a 422 error when the email is not valid', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'not valid',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJson([
        'errors' => [
            'email' => [
                'The email field must be a valid email address.',
            ],
        ],
    ]);
});

it('should return a 422 error when the name is not valid', function () {
    $payload = [
        'name' => null,
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJson([
        'errors' => [
            'name' => [
                'The name field is required.',
            ],
        ],
    ]);
});

it('should dispatch a SendWelcomeNotificationJob', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $this->postJson(route('api.auth.register'), $payload);

    Queue::assertPushed(SendWelcomeNotificationJob::class);
});

it('should send a notification to the user', function () {
    Notification::fake();

    $user = User::factory()->create();

    $action = new WelcomeNotificationAction();
    $action->execute($user);

    Notification::assertSentTo($user, WelcomeNotification::class);
});
