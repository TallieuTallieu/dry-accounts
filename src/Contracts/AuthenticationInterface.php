<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\AuthenticatableInterface;
use Tnt\Account\Contracts\User\UserInterface;

/**
 * Interface for authentication services.
 * Provides methods for user registration, authentication, and session management.
 */
interface AuthenticationInterface
{
    /**
     * Register a new user with the provided credentials.
     * 
     * @param string $authIdentifier User's authentication identifier (email, username, etc.)
     * @param string $password User's password
     * @return UserInterface|null The created user instance or null if registration failed
     */
    public function register(
        string $authIdentifier,
        string $password
    ): ?UserInterface;

    /**
     * Authenticate a user with the provided credentials.
     * 
     * @param string $authIdentifier User's authentication identifier
     * @param string $password User's password
     * @return bool True if authentication was successful, false otherwise
     */
    public function authenticate(
        string $authIdentifier,
        string $password
    ): bool;

    /**
     * Authenticate a user with the provided credentials, but only if the user is activated.
     * 
     * @param string $authIdentifier User's authentication identifier
     * @param string $password User's password
     * @return bool True if authentication was successful and user is activated, false otherwise
     */
    public function authenticateActivated(
        string $authIdentifier,
        string $password
    ): bool;

    /**
     * Log out the currently authenticated user.
     * 
     * @return void
     */
    public function logout();

    /**
     * Check if a user is currently authenticated.
     * 
     * @return bool True if user is authenticated, false otherwise
     */
    public function isAuthenticated(): bool;

    /**
     * Check if a user is authenticated and their account is activated.
     * 
     * @return bool True if user is authenticated and activated, false otherwise
     */
    public function isAuthenticatedAndActivated(): bool;

    /**
     * Get the currently authenticated user.
     * 
     * @return UserInterface|null The authenticated user or null if not authenticated
     */
    public function getUser(): ?UserInterface;

    /**
     * Get an activated user by their authentication identifier.
     * 
     * @param string $authIdentifier User's authentication identifier
     * @return UserInterface|null The activated user or null if not found
     */
    public function getActivatedUser(string $authIdentifier): ?UserInterface;
}
