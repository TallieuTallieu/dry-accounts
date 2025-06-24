<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\UserInterface;

interface UserRepositoryInterface
{
    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|UserInterface
     */
    public function withCredentials(string $authIdentifier, string $password): ?UserInterface;

    /**
     * @param string $authIdentifier
     * @return null|UserInterface
     */
    public function withAuthIdentifier(string $authIdentifier): ?UserInterface;

    /**
     * @param int $id
     * @return null|UserInterface
     */
    public function withIdentifier(int $id): ?UserInterface;

    /**
     * @param string $refreshToken
     * @return null|UserInterface
     */
    public function withValidRefreshToken(string $refreshToken): ?UserInterface;

    /**
     * @param string $authIdentifier
     * @return null|UserInterface
     */
    public function getActivated(string $authIdentifier): ?UserInterface;
}
