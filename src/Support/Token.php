<?php

namespace Tnt\Account\Support;

/**
 * Generator for the single-use tokens that stand in for a credential in a URL
 * (password reset, account activation).
 */
class Token
{
    /**
     * Generate a cryptographically secure 64-character hex token.
     *
     * @return string
     * @throws \Exception If no source of randomness is available
     */
    public static function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
