<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginUserDTO;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class LoginUserAction
{
    public function execute(LoginUserDTO $dto): string
    {
        if (! Auth::attempt($dto->toArray())) {
            throw new InvalidCredentialsException();
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            throw new InvalidCredentialsException();
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
