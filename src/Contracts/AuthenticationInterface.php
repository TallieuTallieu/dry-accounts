<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\AuthenticatableInterface;
use Tnt\Account\Contracts\User\UserInterface;

interface AuthenticationInterface
{
    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|AuthenticatableInterface
     */
    public function register(
        string $authIdentifier,
        string $password
    ): ?UserInterface;

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return bool
     */
    public function authenticate(
        string $authIdentifier,
        string $password
    ): bool;

    /**
     * @return mixed
     */
    public function logout();

    /**
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * @return null|AuthenticatableInterface
     */
    public function getUser(): ?UserInterface;

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function getActivatedUser(string $authIdentifier): ?UserInterface;
}
