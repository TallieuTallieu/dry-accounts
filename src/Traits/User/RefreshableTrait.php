<?php

namespace Tnt\Account\Traits\User;

/**
 * Trait providing default implementation for RefreshableInterface.
 */
trait RefreshableTrait
{
    /**
     * Get the refresh token.
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refresh_token ?? null;
    }

    /**
     * Set the refresh token.
     *
     * @param string|null $token
     * @return static
     */
    public function setRefreshToken(?string $token): static
    {
        $this->refresh_token = $token;
        return $this;
    }

    /**
     * Get the refresh token expiry time.
     *
     * @return int|null
     */
    public function getRefreshTokenExpiryTime(): ?int
    {
        return $this->refresh_token_expiry_time ?? null;
    }

    /**
     * Set the refresh token expiry time.
     *
     * @param int|null $time
     * @return static
     */
    public function setRefreshTokenExpiryTime(?int $time): static
    {
        $this->refresh_token_expiry_time = $time;
        return $this;
    }
}
