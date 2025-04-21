<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\PasswordRecoveryAction;
use App\Actions\Auth\UpdatePasswordAction;
use App\DTOs\Auth\UpdatePasswordDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordRecoveryRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Models\PasswordRecoveryToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PasswordRecoveryController extends Controller
{
    public function generateToken(PasswordRecoveryRequest $request): JsonResponse
    {
        try {
            PasswordRecoveryAction::of(email: (string) $request->string('email'))->generateToken();
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_CREATED);
        }

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

    public function updatePassword(PasswordRecoveryToken $passwordRecoveryToken, UpdatePasswordRequest $request, UpdatePasswordAction $action): JsonResponse
    {
        $action->execute(
            passwordRecoveryToken: $passwordRecoveryToken,
            dto: UpdatePasswordDTO::fromRequest($request)
        );

        return response()->json([
            'message' => 'Your password has been updated.',
        ], Response::HTTP_OK);
    }
}
