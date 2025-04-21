<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\PasswordRecoveryRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Models\PasswordRecoveryToken;
use App\Models\User;
use App\Notifications\PasswordRecoveryTokenNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class PasswordRecoveryController extends Controller
{
    public function generateToken(PasswordRecoveryRequest $request): JsonResponse
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('email', $request->string('email'))
            ->first();

        if (is_null($user)) {
            return response()->json([
                'message' => 'An email with a password recovery link has been sent to your email address.',
            ], Response::HTTP_CREATED);
        }

        $id = $user->id;
        $email = $user->email;
        $now = now()->timestamp;

        $decryptedToken = sprintf('%s:%s:%s', $id, $email, $now);
        $encryptedToken = encrypt($decryptedToken);

        $user->passwordRecoveryTokens()->create([
            'token' => $encryptedToken,
            'expires_at' => now()->addMinutes(5),
        ]);

        $user->notify(new PasswordRecoveryTokenNotification(
            token: $encryptedToken,
        ));

        return response()->json([
            'message' => 'An email with a password recovery link has been sent to your email address.',
        ], Response::HTTP_CREATED);
    }

    public function checkToken(PasswordRecoveryToken $passwordRecoveryToken): JsonResponse
    {
        if ($passwordRecoveryToken->isRevoked()) {
            return response()->json([
                'message' => 'The password recovery link has been revoked.',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json($passwordRecoveryToken->user->toResource(), Response::HTTP_OK);
    }

    public function updatePassword(PasswordRecoveryToken $passwordRecoveryToken, UpdatePasswordRequest $request): JsonResponse
    {
        if ($passwordRecoveryToken->isRevoked()) {
            return response()->json([
                'message' => 'The password recovery link has been revoked.',
            ], Response::HTTP_FORBIDDEN);
        }

        DB::transaction(function () use ($passwordRecoveryToken, $request) {
            $passwordRecoveryToken->user->update([
                'password' => Hash::make((string) $request->string('password')),
            ]);

            $passwordRecoveryToken->update([
                'is_revoked' => true,
            ]);
        });

        return response()->json([
            'message' => 'Your password has been updated.',
        ], Response::HTTP_OK);
    }
}
