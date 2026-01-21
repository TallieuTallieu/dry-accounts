<?php

namespace Tnt\Account\Contracts\User;

/**
 * Combined interface for user functionality.
 *
 * This interface combines authentication, activation, and password reset capabilities
 * for a complete user management system.
 */
interface UserInterface extends
    AuthenticatableInterface,
    ActivatableInterface,
    ResetableInterface
{
    /**
     * Save the user to the database.
     *
     * @return void
     */
    public function save(): void;

    /**
     * Load a user by a specific field value.
     *
     * @param string $field The field name to search by
     * @param mixed $value The value to search for
     * @return static The loaded user instance
     */
    public static function load_by(string $field, mixed $value): static;
}