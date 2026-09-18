<?php

namespace Tnt\Account\Contracts\User;

interface ActivatableInterface
{
  /**
   * Get the activation token for the user.
   * 
   * @return string The activation token
   */
  public function getTempToken(): string;

  /**
   * Get the activation status of the user.
   *
   * @return bool The activation status
   */
  public function isActivated(): bool;

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
   * Check if the activation token is set and has not expired.
   * 
   * @return bool True if a usable activation token exists, false otherwise
   */
  public function isTempTokenValid(): bool;

  /**
   * Get the timestamp at which the activation token was minted.
   * 
   * @return int|null The unix timestamp, or null if unknown
   */
  public function getTempTokenCreated(): ?int;

  /**
   * Get the number of seconds an activation token stays usable.
   * 
   * @return int
   */
  public static function getActivationTokenTtl(): int;

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

  /**
   * Get the temp_token_created field name
   * 
   * @return string
   */
  public static function getTempTokenCreatedField(): string;
}

