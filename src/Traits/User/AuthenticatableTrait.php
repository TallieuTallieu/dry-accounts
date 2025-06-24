<?php

namespace Tnt\Account\Traits\User;

/**
 * Trait providing default implementation for AuthenticatableInterface.
 */
trait AuthenticatableTrait
{
    /**
     * Get the unique identifier for the user.
     *
     * @return int The user's unique identifier
     */
    public function getIdentifier(): int
    {
        return $this->id;
    }

    /**
     * Get the user's hashed password.
     *
     * @return string The hashed password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Get the authentication identifier for the user.
     *
     * @return string|null The authentication identifier, or null if not set
     */
    public function getAuthIdentifier(): ?string
    {
        if (!isset($this->{static::getAuthIdentifierField()})) {
            return null;
        }

        return $this->{static::getAuthIdentifierField()};
    }

    /**
     * Set the user's password using secure hashing.
     * 
     * Only hashes the password if it's not already hashed to prevent double hashing.
     *
     * @param string $password The new password to set
     * @return static The current instance for method chaining
     */
    public function setPassword(string $password): static
    {
        // Check if password is already hashed (password_hash creates strings starting with $)
        if (!$this->isPasswordHashed($password)) {
            $this->password = password_hash($password, PASSWORD_BCRYPT);
        } else {
            $this->password = $password;
        }

        return $this;
    }

    /**
     * Check if a password string is already hashed.
     * 
     * @param string $password The password string to check
     * @return bool True if already hashed, false otherwise
     */
    private function isPasswordHashed(string $password): bool
    {
        // password_hash() creates strings that start with $ and have specific patterns
        // This is a reasonable heuristic to detect already hashed passwords
        return strlen($password) >= 60 && strpos($password, '$') === 0;
    }

    /**
     * Get the email field name
     *
     * @return string
     */
    public static function getAuthIdentifierField(): string
    {
        if (!empty(static::$authIdentifierField)) {
            return static::$authIdentifierField;
        }
        return 'email';
    }

    /**
     * Get the password field name
     *
     * @return string
     */
    public static function getPasswordField(): string
    {
        if (!empty(static::$passwordField)) {
            return static::$passwordField;
        }

        return 'password';
    }
}

