<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterUserDTO;
use App\Jobs\SendWelcomeNotificationJob;
use App\Models\User;

final class RegisterUserAction
{
    public function execute(RegisterUserDTO $dto): User
    {
        $user = User::query()->create($dto->toArray());

        SendWelcomeNotificationJob::dispatch($user->id);

        return $user;
    }
}
