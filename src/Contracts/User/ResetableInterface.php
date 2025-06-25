<?php

namespace Tnt\Account\Contracts\User;

interface ResetableInterface
{
    /**
     * Get the password reset token for the user.
     * 
     * @return string|null The reset token, or null if not set
     */
    public function getResetToken(): ?string;

    /**
     * Set the password reset token for the user.
     * 
     * @param string|null $token The reset token to set, or null to clear
     * @return static The current instance for method chaining
     */
    public function setResetToken(?string $token): self;

    /**
     * Check if a reset token is set for the user.
     * 
     * @return bool True if a reset token exists, false otherwise
     */
    public function hasResetToken(): bool;

    /**
     * Clear the reset token.
     * 
     * @return static The current instance for method chaining
     */
    public function clearResetToken(): self;

    /**
     * Get the reset_token field name
     * 
     * @return string
     */
    public static function getResetTokenField(): string;
}
