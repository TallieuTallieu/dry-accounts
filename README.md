# DRY Accounts
Account system for DRY applications

#### Installation
```ssh
composer require tallieutallieu/dry-accounts

php oak migration migrate -m account
```

##### Config options
All keys live under `accounts.`.

Name                      | Type                    | Default
------------------------- | ------------------------| --------------------------
model                     | dry\orm\Model           | Tnt\Account\Model\User
storage                   | UserStorageInterface    | SessionUserStorage
factory                   | UserFactoryInterface    | UserFactory
repository                | UserRepositoryInterface | UserRepository
auth_class                | AuthenticationInterface | Authentication
use_legacy_hash           | bool                    | false
reset_token_ttl           | int (seconds)           | 3600 (1 hour)
activation_token_ttl      | int (seconds)           | 604800 (7 days)
jwt_secret                | string (min. 32 bytes)  | —
token_expiry_time         | int (seconds)           | 3600
refresh_token_expiry_time | int (seconds)           | 7200

##### Reset and activation tokens

Both tokens are minted with `random_bytes(32)` and stamped with the time they
were created (`reset_token_created` / `temp_token_created`). Check a token's age
before acting on it:

```php
// on the model
$user->isResetTokenValid();
$user->isTempTokenValid();

// or look the user up by a token that has not expired
$userRepository->withValidResetToken($token);
$userRepository->withValidTempToken($token);
```

Tokens minted before these columns existed have no recorded age and are treated
as expired, so outstanding links from before the migration stop working.

#### Basic example usage

##### Usage
```php
<?php

namespace controller;

class authentication
{
    public static function login(Request $request)
    {
        $login_form = new Form( $request);
        
        $login_form->add_email( 'email', [ 'required' => true ] );
        $login_form->add_password( 'password', [ 'required' => true ] );

        $auth_failed = false;
        $is_logged_in = \Tnt\Account\Facade\Auth::isAuthenticated();

        if( $login_form->validate() ) {

            if( \Tnt\Account\Facade\Auth::authenticate( $login_form->get( 'email' ), $login_form->get( 'password' ) ) ) {
                $is_logged_in = true;

            } else {
                $auth_failed = true;
            }
        }

        $tpl = new Template();
        $tpl->login_form = $login_form;
        $tpl->auth_failed = $auth_failed;
        $tpl->is_logged_in = $is_logged_in;
        $tpl->render('users/login.tpl');
    }
    
    public static function register(Request $request)
    {
        $app = Application::get();

        $register_form = new Form( $request);

        $register_failed = false;

        $register_form->add_email('email', [
            'required' => true,
            'extra_validation' => function( $value, &$errors ) use ( &$register_failed, $app )
            {
                if ($app->get(AuthenticationInterface::class)->getActivatedUser($value)) {

                    $errors[] = 'user_already_activated';
                    $register_failed = true;
                }
            }
        ]);

        $register_form->add_password( 'password', ['required' => true,] );

        if( $register_form->validate() ) {

            $user = $app->get(AuthenticationInterface::class)
                ->register(
                    $register_form->get('email'),
                    $register_form->get('password')
                );

            echo 'User registered ' . $user->email;
        }

        $tpl = new Template();
        $tpl->register_form = $register_form;
        $tpl->register_failed = $register_failed;
        $tpl->render('users/register.tpl');
    }

    public static function logout(Request $request)
    {
        \Tnt\Account\Facade\Auth::logout();

        Response::redirect('login/');
    }
}
```