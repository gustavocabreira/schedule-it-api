<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class PasswordRecoveryController extends Controller
{
    public function generateToken(): JsonResponse
    {
        $user = request()->user();

        if (! $user instanceof \App\Models\User) {
            throw new Exception('User not found');
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

        return response()->json([
            'message' => 'An email with a password recovery link has been sent to your email address.',
        ], Response::HTTP_CREATED);
    }
}
