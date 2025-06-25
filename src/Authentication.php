<?php

namespace Tnt\Account;

use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Contracts\UserFactoryInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Events\Authenticated;
use Tnt\Account\Events\Logout;
use Tnt\Account\Events\ResetPassword;

/**
 * Class Authentication
 * @package Tnt\Account
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @var class-string<UserInterface>
     */
    private $model;

    /**
     * @var UserStorageInterface $userStorage
     */
    private $userStorage;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserFactoryInterface
     */
    private $userFactory;

    /**
     * Authentication constructor.
     *
     * @param UserStorageInterface $userStorage User storage implementation
     * @param UserRepositoryInterface $userRepository User repository for data access
     * @param UserFactoryInterface $userFactory Factory for creating users
     * @param string $model User model class name
     */
    public function __construct(
        UserStorageInterface $userStorage,
        UserRepositoryInterface $userRepository,
        UserFactoryInterface $userFactory,
        string $model
    ) {
        $this->model = $model;
        $this->userStorage = $userStorage;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
    }

    /**
     * Reset password for a user by setting a reset token.
     *
     * @param string $authIdentifier User's authentication identifier
     * @return bool True if reset token was set successfully, false otherwise
     */
    public function resetPassword(string $authIdentifier): bool
    {
        $authIdentifierField = $this->model::getAuthIdentifierField();

        try {
            /** @var UserInterface $user */
            $user = $this->model::load_by(
                $authIdentifierField,
                $authIdentifier
            );
            $user->setResetToken();
            $user->save();

            Dispatcher::dispatch(ResetPassword::class, new ResetPassword($user));
            return true;
        } catch (FetchException $exception) {
            return false;
        }
    }

    /**
     * Register a new user with the provided credentials.
     *
     * @param string $authIdentifier User's authentication identifier (email, username, etc.)
     * @param string $password User's password
     * @return UserInterface|null The created user instance or null if registration failed
     */
    public function register(
        string $authIdentifier,
        string $password
    ): ?UserInterface {
        return $this->userFactory->register($authIdentifier, $password);
    }

    /**
     * Authenticate a user with the provided credentials.
     *
     * @param string $authIdentifier User's authentication identifier
     * @param string $password User's password
     * @return bool True if authentication was successful, false otherwise
     */
    public function authenticate(string $authIdentifier, string $password): bool
    {
        if (!$this->userStorage->isEmpty()) {
            $this->userStorage->clear();
        }

        $user = $this->userRepository->withCredentials(
            $authIdentifier,
            $password
        );

        if ($user) {
            // Dispatch the Authenticated event
            Dispatcher::dispatch(
                Authenticated::class,
                new Authenticated($user)
            );

            // Store the user
            $this->userStorage->store($user);

            return true;
        }

        return false;
    }

    /**
     * Authenticate a user with the provided credentials, but only if the user is activated.
     *
     * @param string $authIdentifier User's authentication identifier
     * @param string $password User's password
     * @return bool True if authentication was successful and user is activated, false otherwise
     */
    public function authenticateActivated(
        string $authIdentifier,
        string $password
    ): bool {
        if (!$this->userStorage->isEmpty()) {
            $this->userStorage->clear();
        }

        $user = $this->userRepository->withCredentials(
            $authIdentifier,
            $password
        );

        if ($user && $user->isActivated()) {
            Dispatcher::dispatch(
                Authenticated::class,
                new Authenticated($user)
            );

            $this->userStorage->store($user);

            return true;
        }

        return false;
    }

    /**
     * Log out the currently authenticated user.
     * Clears the user storage and dispatches logout event.
     *
     * @return void
     */
    public function logout()
    {
        if ($this->isAuthenticated()) {
            $user = $this->userStorage->retrieve();
            $this->userStorage->clear();

            // Dispatch the Logout event
            Dispatcher::dispatch(Logout::class, new Logout($user));
        }
    }

    /**
     * Check if a user is currently authenticated.
     *
     * @return bool True if user is authenticated, false otherwise
     */
    public function isAuthenticated(): bool
    {
        return $this->userStorage->isValid();
    }

    /**
     * Check if a user is authenticated and their account is activated.
     *
     * @return bool True if user is authenticated and activated, false otherwise
     */
    public function isAuthenticatedAndActivated(): bool
    {
        $user = $this->userStorage->retrieve();

        if (empty($user)) {
            return false;
        }

        return $user->isActivated();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return UserInterface|null The authenticated user or null if not authenticated
     */
    public function getUser(): ?UserInterface
    {
        return $this->userStorage->retrieve();
    }

    /**
     * Get an activated user by their authentication identifier.
     *
     * @param string $authIdentifier User's authentication identifier
     * @return UserInterface|null The activated user or null if not found
     */
    public function getActivatedUser(string $authIdentifier): ?UserInterface
    {
        return $this->userRepository->getActivated($authIdentifier);
    }
}
