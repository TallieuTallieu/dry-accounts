<?php

namespace Tnt\Account\Facade;

use Oak\Facade;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\User\UserInterface;

/**
 * Authentication facade providing static access to authentication services.
 * 
 * @method static bool resetPassword(string $authIdentifier) Reset password for a user by setting a reset token
 * @method static UserInterface|null register(string $authIdentifier, string $password) Register a new user with credentials
 * @method static bool authenticate(string $authIdentifier, string $password) Authenticate user with any activation status
 * @method static bool authenticateActivated(string $authIdentifier, string $password) Authenticate only activated users
 * @method static void logout() Log out the currently authenticated user and dispatch logout event
 * @method static bool isAuthenticated() Check if a user is currently authenticated
 * @method static bool isAuthenticatedAndActivated() Check if user is authenticated and their account is activated
 * @method static UserInterface|null getUser() Get the currently authenticated user
 * @method static UserInterface|null getActivatedUser(string $authIdentifier) Get an activated user by their identifier
 */
class Auth extends Facade
{
    protected static function getContract(): string
    {
        return AuthenticationInterface::class;
    }
}
