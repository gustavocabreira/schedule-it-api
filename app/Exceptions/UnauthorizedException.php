<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

final class UnauthorizedException extends Exception
{
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Unauthorized',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
