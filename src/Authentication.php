<?php

namespace Tnt\Account;

use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\AuthenticatableInterface;
use Tnt\Account\Contracts\AuthenticationInterface;
use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Contracts\UserFactoryInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Account\Contracts\UserStorageInterface;
use Tnt\Account\Events\Authenticated;
use Tnt\Account\Events\Logout;

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
     * @param string $model
     * @param UserStorageInterface $userStorage
     * @param UserRepositoryInterface $userRepository
     * @param UserFactoryInterface $userFactory
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

    public function resetPassword(string $authIdentifier): bool
    {
        $authIdentifierField = $this->model::getAuthIdentifierField();

        try {
            $user = $this->model::load_by(
                $authIdentifierField,
                $authIdentifier
            );
            $user->setResetToken();
            $user->save();
            return true;
        } catch (FetchException $exception) {
            return false;
        }
    }

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|AuthenticatableInterface
     */
    public function register(
        string $authIdentifier,
        string $password
    ): ?UserInterface {
        return $this->userFactory->register($authIdentifier, $password);
    }

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return bool
     */
    public function authenticate(string $authIdentifier, string $password): bool
    {
        if (!$this->userStorage->isValid()) {
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
        }

        return false;
    }

    /**
     *
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
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return (bool) $this->userStorage->isValid();
    }

    /**
     * @return null|AuthenticatableInterface
     */
    public function getUser(): ?UserInterface
    {
        return $this->userStorage->retrieve();
    }

    /**
     * @param string $authIdentifier
     * @return null|AuthenticatableInterface
     */
    public function getActivatedUser(
        string $authIdentifier
    ): ?UserInterface {
        return $this->userRepository->getActivated($authIdentifier);
    }
}
