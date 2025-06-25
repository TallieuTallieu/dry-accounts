<?php

namespace Tnt\Account\Contracts\User;

interface ActivatableInterface
{
  /**
   * Get the activation token for the user.
   * 
   * @return string The activation token
   */
  public function getToken(): string;

  /**
   * Get the activation status of the user.
   * 
   * @return string The activation status
   */
  public function getIsActivated(): string;

  /**
   * Prepare the user for activation by generating a token.
   * 
   * @param bool $save Whether to save the changes immediately
   * @return static The current instance for method chaining
   */
  public function prepActivate(bool $save = false): self;

  /**
   * Activate the user account.
   * 
   * @param bool $save Whether to save the changes immediately
   * @return static The current instance for method chaining
   */
  public function activate(bool $save = true): self;

  /**
   * Get the is_activated field name
   * 
   * @return string
   */
  public static function getIsActivatedField(): string;

  /**
   * Get the temp_token field name
   * 
   * @return string
   */
  public static function getTempTokenField(): string;
}

