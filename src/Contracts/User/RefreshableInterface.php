<?php

namespace Tnt\Account\Contracts\User;

/**
 * Interface for users that support JWT refresh tokens.
 */
interface RefreshableInterface
{
    /**
     * Get the refresh token.
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string;

    /**
     * Set the refresh token.
     *
     * @param string|null $token
     * @return static
     */
    public function setRefreshToken(?string $token): static;

    /**
     * Get the refresh token expiry time.
     *
     * @return int|null
     */
    public function getRefreshTokenExpiryTime(): ?int;

    /**
     * Set the refresh token expiry time.
     *
     * @param int|null $time
     * @return static
     */
    public function setRefreshTokenExpiryTime(?int $time): static;
}
