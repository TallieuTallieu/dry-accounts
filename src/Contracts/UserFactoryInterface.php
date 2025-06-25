<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\UserInterface;

interface UserFactoryInterface
{
    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|UserInterface
     */
    public function register(string $authIdentifier, string $password): ?UserInterface;
}
