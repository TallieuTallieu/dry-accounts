<?php

namespace Tnt\Account\Contracts;

use Tnt\Account\Contracts\User\AuthenticatableInterface;

interface RegisterableInterface
{
    public static function register(string $identifier, string $password): ?AuthenticatableInterface;
}