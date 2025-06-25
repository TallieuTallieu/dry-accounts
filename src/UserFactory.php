<?php

namespace Tnt\Account;

use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Contracts\UserFactoryInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;

class UserFactory implements UserFactoryInterface
{
    /**
     * @var class-string<UserInterface>
     */
    private $model;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserFactory constructor.
     * @param string $model
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        string $model,
        UserRepositoryInterface $userRepository
    ) {
        $this->model = $model;
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $authIdentifier
     * @param string $password
     * @return null|UserInterface
     * @throws \Exception
     */
    public function register(
        string $authIdentifier,
        string $password
    ): ?UserInterface {
        $existingUser = $this->userRepository->withAuthIdentifier(
            $authIdentifier
        );

        if ($existingUser) {
            return null;
        }

        $newUser = new $this->model();
        $newUser->{$newUser::getIsActivatedField()} = $authIdentifier;
        $newUser->setPassword($password);
        $newUser->save();

        return $newUser;
    }
}

