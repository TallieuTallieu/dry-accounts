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
    public function setResetToken(?string $token = ''): self;

    /**
     * Check if a reset token is set for the user.
     * 
     * @return bool True if a reset token exists, false otherwise
     */
    public function hasResetToken(): bool;

    /**
     * Check if the reset token is set and has not expired.
     *
     * @return bool True if a usable reset token exists, false otherwise
     */
    public function isResetTokenValid(): bool;

    /**
     * Get the timestamp at which the reset token was minted.
     *
     * @return int|null The unix timestamp, or null if unknown
     */
    public function getResetTokenCreated(): ?int;

    /**
     * Get the number of seconds a reset token stays usable.
     *
     * @return int
     */
    public static function getResetTokenTtl(): int;

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

    /**
     * Get the reset_token_created field name
     * 
     * @return string
     */
    public static function getResetTokenCreatedField(): string;
}
