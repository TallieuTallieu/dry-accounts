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
- `UserInterface` - Combines `AuthenticatableInterface`, `ActivatableInterface`, and `ResetableInterface`
- `User` (Model) - Default user model extending dry ORM with traits for each capability
- `UserRepository` - Data access layer for user queries (by credentials, identifier, tokens)
- `UserFactory` - Creates new user instances during registration
- `SessionUserStorage` - Stores authenticated user in PHP session

**Traits (composable user capabilities):**
- `AuthenticatableTrait` - Password hashing/verification, auth identifier field
- `ActivatableTrait` - Account activation with temp tokens
- `ResetableTrait` - Password reset token handling

### Service Provider Registration

`AccountServiceProvider` registers all services with the Oak container and exposes configuration:
- `accounts.model` - User model class (default: `Tnt\Account\Model\User`)
- `accounts.storage` - User storage implementation
- `accounts.factory` - User factory implementation
- `accounts.repository` - User repository implementation
- `accounts.use_legacy_hash` - Enable legacy MD5+salt password verification

### Events

Events dispatched via Oak Dispatcher: `Authenticated`, `Logout`, `Created`, `Activated`, `ResetPassword`, `ResendActivated`

### Database Migrations

Run with: `php oak migration migrate -m accounts`

Tables: `account_user` with fields dynamically created based on which interfaces the model implements.
