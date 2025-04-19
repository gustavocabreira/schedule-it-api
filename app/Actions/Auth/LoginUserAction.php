<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginUserDTO;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class LoginUserAction
{
    public function execute(LoginUserDTO $dto): string
    {
        if (! Auth::attempt($dto->toArray())) {
            throw new Exception('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            throw new Exception('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
