# Changelog

All notable changes to this package are documented in this file. Versions
follow [Semantic Versioning](https://semver.org). New entries are generated from
commit messages by [dry-ci](https://github.com/TallieuTallieu/dry-ci); past
entries may be edited by hand.


## 4.0.1 - 2026-09-18

### Breaking changes

- **deps:** Requires PHP 8.4 or later ([sc-11479](https://app.shortcut.com/tallieu--tallieu/story/11479))

### Other changes

- **deps:** Support oak 4 ([sc-11479](https://app.shortcut.com/tallieu--tallieu/story/11479))

## 4.0.0 - 2026-09-18

### Breaking changes

- **accounts:** `ResetableInterface`, `ActivatableInterface` and `UserRepositoryInterface` gain methods (`isResetTokenValid()`, `getResetTokenCreated()`, `getResetTokenTtl()`, `getResetTokenCreatedField()`, `isTempTokenValid()`, `getTempTokenCreated()`, `getActivationTokenTtl()`, `getTempTokenCreatedField()`, `withValidResetToken()`, `withValidTempToken()`). Custom models that implement these interfaces without the package traits, and custom repositories that do not extend `UserRepository`, must implement them ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))
- **accounts:** Run the `account` migrator (`php oak migration migrate -m account`) to add the `reset_token_created` and `temp_token_created` columns. Reset and activation tokens minted before this migration have no recorded age and count as expired, so outstanding reset and activation links stop working ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))

### Features

- **accounts:** Reset and activation tokens expire after `accounts.reset_token_ttl` (default 1 hour) and `accounts.activation_token_ttl` (default 7 days); check them with `isResetTokenValid()` / `isTempTokenValid()` or look users up with `UserRepository::withValidResetToken()` / `withValidTempToken()` ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))

### Fixes

- **accounts:** `setPassword()` no longer stores a password verbatim when it merely looks like a hash (60+ characters starting with `$`); it now checks with `password_get_info()` ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))
- **accounts:** Reset and activation tokens are generated with `random_bytes(32)` instead of the predictable `uniqid()` ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))
- **accounts:** `setResetToken(null)` clears the reset token instead of minting a new one ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))
- **accounts:** `prepActivate()` and `activate()` respect a model's custom field names ([sc-11447](https://app.shortcut.com/tallieu--tallieu/story/11447))
- **docs:** The README's migrate command uses the migrator's real name, `-m account`; `-m accounts` ran no revisions

### Other changes

- **docs:** Document all config keys in the README

## Earlier history

- **1.0.0** (2019-10-07): First release: session-based authentication (`Auth` facade, `AuthenticationInterface`), account events and an auth controller with basic API routes.
- **1.0.2** (2020-01-16): A `User` model, database revisions and a default `UserRepository`; in 1.0.4 the user table was renamed to `account_user`.
- **1.0.6** (2020-06-18): Refresh token methods.
- **1.0.8** (2021-03-23): Repository moved to Tallieu & Tallieu; 1.0.9 (2024-04-30) upgraded `lindelius/php-jwt` from 0.8.1 to 0.9.1.
- **1.0.10** (2025-06-23): Password reset support.
- **2.0.0** (2025-06-24): Refactor: user contracts in `Contracts\User` (`UserInterface`, `AuthenticatableInterface`, `ActivatableInterface`, `ResetableInterface`) with default traits, the migration built from the model's interfaces, and the old `Contracts\AuthenticatableInterface` removed in favour of `UserInterface`. Requires dry-external-api 2.
- **2.0.10** (2025-06-25): `authenticateActivated()`; 2.0.15–2.0.17 dispatch a `ResetPassword` event on password reset.
- **2.0.18** (2025-07-02): The authentication class is configurable with `accounts.auth_class`.
- **2.1.0** (2025-07-03): Extra data can be passed to `UserFactory` (and, since 2.0.20, to `register()`).
- **3.0.0** (2025-08-29): Breaking: moves to the dry v3 stack (oak ^3.0.2, dry-dbi ^3.0.0, dry-external-api ^3.0.0). The 2.1.x line (2.1.2–2.1.4) kept receiving backports for the old stack.
- **3.0.1** (2025-09-09): `ResendActivatedEvent`; 3.0.2 fixed a missing `FetchException` import.
- **3.0.3** (2025-09-17): Optional legacy MD5+salt password hashing (`accounts.use_legacy_hash`); requires dry v3.3.0-beta.18.
- **3.0.4** (2026-01-21): Support for dry 3.8; 3.0.5 replaced the abandoned `lindelius/php-jwt` with `firebase/php-jwt` and added `RefreshableInterface` for JWT refresh tokens.
- **3.0.6** (2026-03-16): `firebase/php-jwt` 7.0.3 and dry v3.8.1-beta.5; logs a warning when `accounts.jwt_secret` is shorter than 32 bytes.
- **3.0.7** (2026-05-06): Allows dry 4.

See the git tags before 4.0.0 for the full history.
