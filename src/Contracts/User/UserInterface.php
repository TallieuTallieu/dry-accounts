<?php

namespace Tnt\Account\Contracts\User;

/**
 * Combined interface for user functionality.
 *
 * This interface combines authentication, activation, password reset,
 * and refresh token capabilities for a complete user management system.
 */
interface UserInterface extends
    AuthenticatableInterface,
    ActivatableInterface,
    ResetableInterface,
    RefreshableInterface
{
    /**
     * Save the user to the database.
     *
     * @return void
     */
    public function save(): void;
}