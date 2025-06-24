<?php

namespace Tnt\Account;

use dry\db\FetchException;
use Tnt\Account\Contracts\User\AuthenticatableInterface;
use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Contracts\UserRepositoryInterface;
use Tnt\Dbi\BaseRepository;
use Tnt\Dbi\Contracts\CriteriaCollectionInterface;
use Tnt\Dbi\Criteria\Equals;
use Tnt\Dbi\Criteria\GreaterThan;
use Tnt\Dbi\Criteria\IsTrue;

/**
 * Repository for managing user data operations.
 *
 * Provides methods for querying users based on various criteria such as
 * authentication credentials, identifiers, tokens, and activation status.
 *
 * @property class-string<UserInterface> $model
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var class-string<UserInterface> The fully qualified class name of the user model
     */
    protected $model;

    /**
     * UserRepository constructor.
     *
     * @param class-string<UserInterface> $model The fully qualified class name of the user model
     * @param CriteriaCollectionInterface $criteria The criteria collection for query building
     */
    public function __construct(
        string $model,
        CriteriaCollectionInterface $criteria
    ) {
        $this->model = $model;

        parent::__construct($criteria);
    }

    /**
     * Find a user by authentication credentials.
     *
     * Searches for a user with the given authentication identifier and password.
     * Uses MD5 hashing with salt for password comparison (legacy support).
     *
     * @param string $authIdentifier The authentication identifier (e.g., email)
     * @param string $password The plain text password to verify
     * @return UserInterface|null The user if found and credentials match, null otherwise
     */
    public function withCredentials(
        string $authIdentifier,
        string $password
    ): ?UserInterface {
        try {
            $this->addCriteria(
                new Equals(
                    $this->model::getAuthIdentifierName(),
                    $authIdentifier
                )
            );

            $user = $this->first();

            $passwordMatches = password_verify(
                $password,
                $user->{$user::getPasswordField()}
            );
            if ($passwordMatches) {
                return $user;
            }
        } catch (FetchException $e) {
        }
        return null;
    }

    /**
     * Find a user by their authentication identifier.
     *
     * @param string $authIdentifier The authentication identifier (e.g., email)
     * @return UserInterface|null The user if found, null otherwise
     */
    public function withAuthIdentifier(string $authIdentifier): ?UserInterface
    {
        try {
            $this->addCriteria(
                new Equals(
                    $this->model::getAuthIdentifierField(),
                    $authIdentifier
                )
            );

            return $this->first();
        } catch (FetchException $e) {
            return null;
        }
    }

    /**
     * Find a user by their unique identifier.
     *
     * @param int $id The user's unique identifier
     * @return UserInterface|null The user if found, null otherwise
     */
    public function withIdentifier(int $id): ?UserInterface
    {
        try {
            $this->addCriteria(new Equals('id', $id));

            return $this->first();
        } catch (FetchException $e) {
            return null;
        }
    }

    /**
     * Find a user by valid refresh token.
     *
     * Searches for a user with the given refresh token that has not yet expired.
     *
     * @param string $refreshToken The refresh token to search for
     * @return UserInterface|null The user if found with valid token, null otherwise
     */
    public function withValidRefreshToken(
        string $refreshToken
    ): ?UserInterface {
        try {
            $this->addCriteria(new Equals('refresh_token', $refreshToken));
            $this->addCriteria(
                new GreaterThan('refresh_token_expiry_time', time())
            );

            return $this->first();
        } catch (FetchException $e) {
            return null;
        }
    }

    /**
     * Find an activated user by their authentication identifier.
     *
     * Searches for a user with the given authentication identifier who has
     * been activated (is_activated = true).
     *
     * @param string $authIdentifier The authentication identifier (e.g., email)
     * @return UserInterface|null The activated user if found, null otherwise
     */
    public function getActivated(string $authIdentifier): ?UserInterface
    {
        try {
            $this->addCriteria(
                new Equals(
                    $this->model::getAuthIdentifierField(),
                    $authIdentifier
                )
            );
            $this->addCriteria(new IsTrue($this->model::getIsActivatedField()));

            return $this->first();
        } catch (FetchException $e) {
            return null;
        }
    }
}
