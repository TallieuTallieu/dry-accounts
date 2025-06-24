<?php

namespace Tnt\Account\Events;

use Oak\Dispatcher\Event;
use Tnt\Account\Contracts\User\UserInterface;

/**
 * Class UserEvent
 * @package Tnt\Account\Events
 */
abstract class UserEvent extends Event
{
    /**
     * @var UserInterface $user
     */
    private $user;

    /**
     * Activated constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
