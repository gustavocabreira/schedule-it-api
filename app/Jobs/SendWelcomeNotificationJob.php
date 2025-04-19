<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class SendWelcomeNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId);
        $user->notify(new WelcomeNotification());
    }
}
