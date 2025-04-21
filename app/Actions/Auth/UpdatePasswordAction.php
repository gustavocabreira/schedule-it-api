<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\UpdatePasswordDTO;
use App\Models\PasswordRecoveryToken;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UpdatePasswordAction
{
    public function execute(PasswordRecoveryToken $passwordRecoveryToken, UpdatePasswordDTO $dto): void
    {
        if ($passwordRecoveryToken->isRevoked()) {
            throw new Exception('The password recovery link has been revoked.', Response::HTTP_FORBIDDEN);
        }

        DB::transaction(function () use ($passwordRecoveryToken, $dto) {
            $passwordRecoveryToken->user->update([
                'password' => Hash::make((string) $dto->password),
            ]);

            $passwordRecoveryToken->update([
                'is_revoked' => true,
            ]);
        });
    }
}
