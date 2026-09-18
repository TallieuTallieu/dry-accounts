<?php

namespace Tnt\Account\Traits\User;

use Oak\Config\Facade\Config;
use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Events\Activated;
use Tnt\Account\Support\Token;

/**
 * Trait providing default implementation for ActivatableInterface.
 */
trait ActivatableTrait
{
    /**
     * Override this property to use a custom is_activated field.
     */
    protected static string $isActivatedField = '';

    /**
     * Override this property to use a custom temp_token field.
     */
    protected static string $tempTokenField = '';

    /**
     * Override this property to use a custom temp_token_created field.
     */
    protected static string $tempTokenCreatedField = '';

    /**
     * Get the activation token for the user.
     *
     * @return string The activation token
     */
    public function getTempToken(): string
    {
        return $this->{static::getTempTokenField()};
    }

    /**
     * Get the activation status of the user.
     *
     * @return bool The activation status
     */
    public function isActivated(): bool
    {
        return (bool) $this->{static::getIsActivatedField()};
    }

    /**
     * Prepare the user for activation by generating a token.
     *
     * @param bool $save Whether to save the changes immediately
     * @return static The current instance for method chaining
     */
    public function prepActivate(bool $save = false): self
    {
        $this->{static::getTempTokenField()} = Token::generate();
        $this->{static::getTempTokenCreatedField()} = time();
        $this->{static::getIsActivatedField()} = false;

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * Activate the user account.
     *
     * @param bool $save Whether to save the changes immediately
     * @return static The current instance for method chaining
     */
    public function activate(bool $save = true): self
    {
        $this->{static::getIsActivatedField()} = true;
        $this->{static::getTempTokenField()} = null;
        $this->{static::getTempTokenCreatedField()} = null;

        if ($save) {
            $this->save();
        }

        Dispatcher::dispatch(Activated::class, new Activated($this));

        return $this;
    }

    /**
     * Check if the activation token is set and has not expired.
     *
     * A token minted before the temp_token_created column existed has no age
     * and is treated as expired.
     *
     * @return bool True if a usable activation token exists, false otherwise
     */
    public function isTempTokenValid(): bool
    {
        if (empty($this->{static::getTempTokenField()})) {
            return false;
        }

        $created = $this->getTempTokenCreated();

        if ($created === null) {
            return false;
        }

        return $created + static::getActivationTokenTtl() > time();
    }

    /**
     * Get the timestamp at which the activation token was minted.
     *
     * @return int|null The unix timestamp, or null if unknown
     */
    public function getTempTokenCreated(): ?int
    {
        $created = $this->{static::getTempTokenCreatedField()} ?? null;

        return $created === null ? null : (int) $created;
    }

    /**
     * Get the number of seconds an activation token stays usable.
     *
     * Activation links are mailed once and often opened days later, so this
     * defaults to a longer window than the reset token.
     *
     * @return int The configured TTL, seven days by default
     */
    public static function getActivationTokenTtl(): int
    {
        return (int) (Config::get('accounts.activation_token_ttl') ?? 604800);
    }

    /**
     * Get the is_activated field name
     *
     * @return string
     */
    public static function getIsActivatedField(): string
    {
        if (!empty(static::$isActivatedField)) {
            return static::$isActivatedField;
        }

        return 'is_activated';
    }

    /**
     * Get the temp_token field name
     *
     * @return string
     */
    public static function getTempTokenField(): string
    {
        if (!empty(static::$tempTokenField)) {
            return static::$tempTokenField;
        }

        return 'temp_token';
    }

    /**
     * Get the temp_token_created field name
     *
     * @return string
     */
    public static function getTempTokenCreatedField(): string
    {
        if (!empty(static::$tempTokenCreatedField)) {
            return static::$tempTokenCreatedField;
        }

        return 'temp_token_created';
    }
}

