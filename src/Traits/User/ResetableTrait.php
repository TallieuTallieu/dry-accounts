<?php

namespace Tnt\Account\Traits\User;

use Oak\Config\Facade\Config;
use Tnt\Account\Support\Token;

/**
 * Trait providing default implementation for ResetableInterface.
 */
trait ResetableTrait
{
    /**
     * Override this property to use a custom reset_token field.
     */
    protected static string $resetTokenName = '';

    /**
     * Override this property to use a custom reset_token_created field.
     */
    protected static string $resetTokenCreatedField = '';

    /**
     * Get the password reset token for the user.
     *
     * @return string|null The reset token, or null if not set
     */
    public function getResetToken(): ?string
    {
        return $this->{static::getResetTokenField()};
    }

    /**
     * Set the password reset token for the user.
     *
     * @param string|null $token The reset token to set, null to clear it, or an empty string (default) to generate one
     * @return static The current instance for method chaining
     */
    public function setResetToken(?string $token = ''): self
    {
        // null must fall through: it clears the token.
        $token = $token === '' ? Token::generate() : $token;

        $this->{static::getResetTokenField()} = $token;
        $this->{static::getResetTokenCreatedField()} =
            $token === null ? null : time();

        return $this;
    }

    /**
     * Check if a reset token is set for the user.
     *
     * @return bool True if a reset token exists, false otherwise
     */
    public function hasResetToken(): bool
    {
        return !empty($this->getResetToken());
    }

    /**
     * Check if the reset token is set and has not expired.
     *
     * A token minted before the reset_token_created column existed has no age
     * and is treated as expired.
     *
     * @return bool True if a usable reset token exists, false otherwise
     */
    public function isResetTokenValid(): bool
    {
        if (!$this->hasResetToken()) {
            return false;
        }

        $created = $this->getResetTokenCreated();

        if ($created === null) {
            return false;
        }

        return $created + static::getResetTokenTtl() > time();
    }

    /**
     * Get the timestamp at which the reset token was minted.
     *
     * @return int|null The unix timestamp, or null if unknown
     */
    public function getResetTokenCreated(): ?int
    {
        $created = $this->{static::getResetTokenCreatedField()} ?? null;

        return $created === null ? null : (int) $created;
    }

    /**
     * Clear the reset token.
     *
     * @return static The current instance for method chaining
     */
    public function clearResetToken(): self
    {
        $this->{static::getResetTokenField()} = null;
        $this->{static::getResetTokenCreatedField()} = null;

        return $this;
    }

    /**
     * Get the number of seconds a reset token stays usable.
     *
     * @return int The configured TTL, one hour by default
     */
    public static function getResetTokenTtl(): int
    {
        return (int) (Config::get('accounts.reset_token_ttl') ?? 3600);
    }

    /**
     * Get the reset_token field name
     *
     * @return string
     */
    public static function getResetTokenField(): string
    {
        if (!empty(static::$resetTokenName)) {
            return static::$resetTokenName;
        }

        return 'reset_token';
    }

    /**
     * Get the reset_token_created field name
     *
     * @return string
     */
    public static function getResetTokenCreatedField(): string
    {
        if (!empty(static::$resetTokenCreatedField)) {
            return static::$resetTokenCreatedField;
        }

        return 'reset_token_created';
    }
}
