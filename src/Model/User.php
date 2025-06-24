<?php

namespace Tnt\Account\Model;

use dry\orm\Model;
use dry\orm\special\Boolean;
use Oak\Dispatcher\Facade\Dispatcher;
use Tnt\Account\Contracts\User\UserInterface;
use Tnt\Account\Events\Created;
use Tnt\Account\Traits\User\AuthenticatableTrait;
use Tnt\Account\Traits\User\ActivatableTrait;
use Tnt\Account\Traits\User\ResetableTrait;

/**
 * User model class representing an account user.
 * 
 * @property int $id User unique identifier
 * @property string $email User email address
 * @property string $password Hashed password
 * @property string $password_salt Password salt for hashing
 * @property string $temp_token Temporary activation token
 * @property string $reset_token Password reset token
 * @property bool $is_activated Whether the user account is activated
 * @property int $created Timestamp when user was created
 * @property int $updated Timestamp when user was last updated
 */
class User extends Model implements UserInterface
{
    use AuthenticatableTrait, ActivatableTrait, ResetableTrait;

    const TABLE = 'account_user';

    public static $special_fields = [
        'is_activated' => Boolean::class,
    ];

    public function save()
    {
        if (!$this->id) {
            $this->created = time();
            $this->updated = time();

            $this->setPassword($this->password);
            $this->prepActivate();
            parent::save();

            Dispatcher::dispatch(Created::class, new Created($this));
            return;
        }

        $this->updated = time();
        parent::save();
    }

}
