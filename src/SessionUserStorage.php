<?php

namespace Tnt\Account;

use Oak\Session\Facade\Session;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;

/**
 * Session-based user storage implementation.
 * Stores user identifiers in the session and retrieves users via repository.
 */
class SessionUserStorage implements UserStorageInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * SessionUserStorage constructor.
     *
     * @param UserRepositoryInterface $userRepository Repository for user data access
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Store a user identifier in the session.
     *
     * @param UserInterface $user The user to store
     * @return void
     */
    public function store(UserInterface $user)
    {
        Session::set('user', $user->getIdentifier());
        Session::save();
    }

    /**
     * Retrieve the user from session using stored identifier.
     *
     * @return UserInterface|null The user instance or null if not found
     */
    public function retrieve(): ?UserInterface
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->userRepository->withIdentifier(Session::get('user'));
    }

    /**
     * Check if the session contains a valid user.
     *
     * @return bool True if session is valid and user exists, false otherwise
     */
    public function isValid(): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        $user = $this->userRepository->withIdentifier(Session::get('user'));

        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * Check if the session storage is empty (no user identifier stored).
     * 
     * @return bool True if session is empty, false otherwise
     */
    public function isEmpty(): bool
    {
        return !Session::has('user') || empty(Session::get('user'));
    }

    /**
     * Clear the user identifier from the session.
     *
     * @return void
     */
    public function clear()
    {
        Session::set('user', null);
        Session::save();
    }
}
