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
}