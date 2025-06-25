<?php

namespace Tnt\Account\Traits\User;

/**
 * Trait providing default implementation for ResetableInterface.
 */
trait ResetableTrait
{
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
     * @param string|null $token The reset token to set, or null to clear, pass an empty string (default) to use uniqid
     * @return static The current instance for method chaining
     */
    public function setResetToken(?string $token = ''): self
    {
        if (empty($token)) {
            $token = uniqid('reset_', true);
        }
        $this->{static::getResetTokenField()} = $token;
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
     * Clear the reset token.
     *
     * @return static The current instance for method chaining
     */
    public function clearResetToken(): self
    {
        $this->{static::getResetTokenField()} = null;
        return $this;
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
}
