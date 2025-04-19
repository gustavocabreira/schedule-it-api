<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterUserDTO;
use App\Models\User;

final class RegisterUserAction
{
    public function execute(RegisterUserDTO $dto): User
    {
        return User::query()->create($dto->toArray());
    }
}
