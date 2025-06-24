<?php

namespace Tnt\Account\Contracts\User;

interface AuthenticatableInterface
{
    /**
     * Get the unique identifier for the user.
     * 
     * @return int The user's unique identifier
     */
    public function getIdentifier(): int;

    /**
     * Get the authentication identifier for the user.
     * 
     * @return string|null The authentication identifier, or null if not set
     */
    public function getAuthIdentifier(): ?string;

    /**
     * Get the user's hashed password.
     * 
     * @return string The hashed password
     */
    public function getPassword(): string;

    /**
     * Set the user's password.
     * 
     * @param string $password The new password to set
     * @return static The current instance for method chaining
     */
    public function setPassword(string $password): static;

    /**
     * Get the email field name
     * 
     * @return string
     */
    public static function getAuthIdentifierField(): string;

    /**
     * Get the password field name
     * 
     * @return string
     */
    public static function getPasswordField(): string;
}

