<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\UserInterface;

interface UserStorageInterface
{
    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function store(UserInterface $user);

    /**
     * @return null|UserInterface
     */
    public function retrieve(): ?UserInterface;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return mixed
     */
    public function clear();
}
