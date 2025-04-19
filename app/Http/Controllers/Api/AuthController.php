<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\LoginUserDTO;
use App\DTOs\Auth\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterUserRequest $request, RegisterUserAction $action): JsonResponse
    {
        $user = $action->execute(RegisterUserDTO::fromRequest($request));

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Login a user
     */
    public function login(LoginUserRequest $request, LoginUserAction $action): JsonResponse
    {
        $token = $action->execute(LoginUserDTO::fromRequest($request));

        return response()->json([
            'access_token' => $token,
        ], Response::HTTP_OK);
    }
}
