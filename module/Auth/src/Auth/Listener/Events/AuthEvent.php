<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Listener\Events;

use Auth\Entity\User;
use Zend\EventManager\Event;

class AuthEvent extends Event
{
    /**
     * User events triggered by eventmanager
     */

    /**
     * a new password was created
     */
    const EVENT_AUTH_NEWPASSWORD   = 'auth.newpassword';

    const EVENT_USER_REGISTERED    = 'auth.user-registered';

    const EVENT_USER_CONFIRMED     = 'auth.user-confirmed';

    protected $userEntity;

    protected $newPassword;

    protected $resetLink;

    protected $user;


    public function setUserEntity($userEntity)
    {
        $this->userEntity = $userEntity;
        return $this;
    }

    public function getUserEntity()
    {
        return $this->userEntity;
    }

    public function setNewPassword($password)
    {
        $this->newPassword = $password;
        return $this;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function setResetLink($resetLink)
    {
        $this->resetLink = $resetLink;
        return $this;
    }

    public function getResetLink()
    {
        return $this->resetLink;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
}
