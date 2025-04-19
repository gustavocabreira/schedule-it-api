<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\User;
use App\Notifications\WelcomeNotification;

final class WelcomeNotificationAction
{
    public function execute(User $user): void
    {
        $user->notify(new WelcomeNotification);
    }
}
