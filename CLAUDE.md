# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

dry-accounts is an account/authentication system library for DRY framework applications. It provides user registration, authentication, password reset, and session management functionality.

## Development Commands

All commands run through Docker via Make:

```bash
make docker          # Start the Docker container
make docker-exec     # Shell into the container
make phpstan         # Run PHPStan static analysis (level 6)
```

## Architecture

### Core Components

**Authentication Flow:**
- `Authentication` - Main authentication service handling login, logout, registration, and password reset
- `AuthController` - REST API controller for JWT-based authentication (authenticate, authorize, refresh-token endpoints)
- `Auth` (Facade) - Static facade for accessing authentication methods

**User System:**
- `UserInterface` - Combines `AuthenticatableInterface`, `ActivatableInterface`, `ResetableInterface`, and `RefreshableInterface`
- `User` (Model) - Default user model extending dry ORM with traits for each capability
- `UserRepository` - Data access layer for user queries (by credentials, identifier, tokens)
- `UserFactory` - Creates new user instances during registration
- `SessionUserStorage` - Stores authenticated user in PHP session
- `Support\Token` - Generates reset and activation tokens (`random_bytes`, never `uniqid()`)

**Traits (composable user capabilities):**
- `AuthenticatableTrait` - Password hashing/verification, auth identifier field
- `ActivatableTrait` - Account activation with temp tokens
- `ResetableTrait` - Password reset token handling
- `RefreshableTrait` - JWT refresh token storage

Reset and activation tokens record when they were minted (`reset_token_created` / `temp_token_created`) and expire after their TTL. Check with `isResetTokenValid()` / `isTempTokenValid()` or look up via `UserRepository::withValidResetToken()` / `withValidTempToken()`.

Custom column names are set by overriding the static `get*Field()` getters. The `$*Field` properties the docblocks mention cannot be overridden (PHP fatal error, see sc-11467).

### Service Provider Registration

`AccountServiceProvider` registers all services with the Oak container and exposes configuration:
- `accounts.model` - User model class (default: `Tnt\Account\Model\User`)
- `accounts.storage` - User storage implementation
- `accounts.factory` - User factory implementation
- `accounts.repository` - User repository implementation
- `accounts.auth_class` - Authentication implementation
- `accounts.use_legacy_hash` - Enable legacy MD5+salt password verification
- `accounts.reset_token_ttl` - Reset token lifetime in seconds (default: 3600)
- `accounts.activation_token_ttl` - Activation token lifetime in seconds (default: 604800)
- `accounts.jwt_secret` - JWT signing secret, minimum 32 bytes
- `accounts.token_expiry_time` / `accounts.refresh_token_expiry_time` - JWT lifetimes in seconds (defaults: 3600 / 7200)

### Events

Events dispatched via Oak Dispatcher: `Authenticated`, `Logout`, `Created`, `Activated`, `ResetPassword`, `ResendActivated`

### Database Migrations

Run with: `php oak migration migrate -m account` (the migrator is named `account`, singular)

Tables: `account_user` with fields dynamically created based on which interfaces the model implements.
