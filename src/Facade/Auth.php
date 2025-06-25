<?php

namespace Tnt\Account\Facade;

use Oak\Facade;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\User\UserInterface;

/**
 * @method static UserInterface|null register(string $authIdentifier, string $password)
 * @method static bool authenticate(string $authIdentifier, string $password)
 * @method static mixed logout()
 * @method static bool isAuthenticated()
 * @method static UserInterface|null getUser()
 * @method static UserInterface|null getActivatedUser(string $authIdentifier)
 */
class Auth extends Facade
{
    protected static function getContract(): string
    {
        return AuthenticationInterface::class;
    }
}