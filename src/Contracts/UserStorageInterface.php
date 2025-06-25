<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\UserInterface;

/**
 * Interface for user storage implementations.
 * Handles storing, retrieving, and managing user sessions.
 */
interface UserStorageInterface
{
    /**
     * Store a user in the storage mechanism.
     * 
     * @param UserInterface $user The user to store
     * @return void
     */
    public function store(UserInterface $user);

    /**
     * Retrieve the stored user from the storage mechanism.
     * 
     * @return UserInterface|null The stored user or null if not found
     */
    public function retrieve(): ?UserInterface;

    /**
     * Check if the stored user session is valid.
     * 
     * @return bool True if the session is valid, false otherwise
     */
    public function isValid(): bool;

    /**
     * Check if the storage is empty (no user stored).
     * 
     * @return bool True if storage is empty, false otherwise
     */
    public function isEmpty(): bool;

    /**
     * Clear the stored user from the storage mechanism.
     * 
     * @return void
     */
    public function clear();
}
