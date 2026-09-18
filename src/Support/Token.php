<?php

namespace Tnt\Account\Support;

/**
 * Generator for the single-use tokens that stand in for a credential in a URL
 * (password reset, account activation).
 */
class Token
{
    /**
     * Generate a cryptographically secure token.
     *
     * uniqid() is the current time in microseconds plus lcg_value(), which is
     * largely predictable from a known timestamp, so it must not be used here.
     *
     * @param int $bytes Number of random bytes; the token is twice as long in hex
     * @return string A hex token of $bytes * 2 characters
     * @throws \Exception If no source of randomness is available
     */
    public static function generate(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }
}
