<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;

it('should generate a password recovery token', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('api.auth.password-recovery.generate-token'));
    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas('password_recovery_tokens', [
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseCount('password_recovery_tokens', 1);
});
