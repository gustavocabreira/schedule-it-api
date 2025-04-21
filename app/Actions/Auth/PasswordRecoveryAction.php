<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use App\Notifications\PasswordRecoveryTokenNotification;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PasswordRecoveryAction
{
    public function __construct(
        private User $user,
    ) {}

    public static function of(string $email): self
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (is_null($user)) {
            throw new NotFoundHttpException('An email with a password recovery link has been sent to your email address.');
        }

        return new self(
            user: $user,
        );
    }

    public function generateToken(): void
    {
        $id = $this->user->id;
        $email = $this->user->email;
        $now = now()->timestamp;

        $decryptedToken = sprintf('%s:%s:%s', $id, $email, $now);
        $encryptedToken = encrypt($decryptedToken);

        $this->user->passwordRecoveryTokens()->create([
            'token' => $encryptedToken,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->user->notify(new PasswordRecoveryTokenNotification(
            token: $encryptedToken,
        ));
    }
}
