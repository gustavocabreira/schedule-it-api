<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\User\UpdatePasswordRequest;

final class UpdatePasswordDTO
{
    public function __construct(
        public readonly string $password,
    ) {}

    public static function fromRequest(UpdatePasswordRequest $request): self
    {
        return new self(
            password: (string) $request->string('password'),
        );
    }

    /**
     * @return array{password: string}
     */
    public function toArray(): array
    {
        return [
            'password' => $this->password,
        ];
    }
}
