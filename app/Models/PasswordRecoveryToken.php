<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $user_id
 * @property-read string $token
 * @property-read \Illuminate\Support\Carbon $expires_at
 * @property-read bool $is_revoked
 * @property-read User $user
 */
final class PasswordRecoveryToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'is_revoked',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the token is revoked.
     */
    public function isRevoked(): bool
    {
        if ($this->expires_at->isPast()) {
            return true;
        }

        return $this->is_revoked;
    }
}
