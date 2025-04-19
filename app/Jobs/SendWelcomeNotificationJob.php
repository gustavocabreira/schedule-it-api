<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Notifications\WelcomeNotificationAction;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class SendWelcomeNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $userId,
    ) {}

    public function handle(WelcomeNotificationAction $action): void
    {
        $action->execute(
            user: User::query()->findOrFail($this->userId)
        );
    }
}
