<?php

namespace Tnt\Account\Traits\User;

use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Events\Activated;

/**
 * Trait providing default implementation for ActivatableInterface.
 */
trait ActivatableTrait
{
    /**
     * Get the activation token for the user.
     *
     * @return string The activation token
     */
    public function getToken(): string
    {
        return $this->{static::getTempTokenField()};
    }

    /**
     * Get the activation status of the user.
     *
     * @return string The activation status
     */
    public function getIsActivated(): string
    {
        return $this->is_activated;
    }

    /**
     * Prepare the user for activation by generating a token.
     *
     * @param bool $save Whether to save the changes immediately
     * @return static The current instance for method chaining
     */
    public function prepActivate(bool $save = false): self
    {
        $this->{static::$tokenName} = uniqid('activate_', true);
        $this->is_activated = false;

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
        $this->is_activated = true;
        $this->temp_token = null;

        if ($save) {
            $this->save();
        }

        Dispatcher::dispatch(Activated::class, new Activated($this));

        return $this;
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
}

