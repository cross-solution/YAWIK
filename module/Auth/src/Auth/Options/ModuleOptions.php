<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * defines AbstractOptions of the Auth Module
 * @since 0.30
 *        - add $notifyOnRegistration, $notificationEmail and notificationLanguage options.
 */
class ModuleOptions extends AbstractOptions
{

    /**
     * default email address, which is used in FROM headers of system mails like "new registration",
     * "forgot password",..
     *
     * @var string
     */
    protected $fromEmail = 'email@example.com';

    /**
     * default name, which is used in FROM headers of system mails like "new registration", "forgot password",..
     *
     * @var string
     */
    protected $fromName = 'Name';

    /**
     * default role, which is assigned to a user after registration. possible Values (user|recruiter)
     * @var string
     */
    protected $role = 'recruiter';

    /**
     * Subject of the registration mail.
     *
     * @var string
     */
    protected $mailSubjectRegistration = 'Welcome to YAWIK';

    /**
     * an authSuffix can be used, if you plan to connect an external system. Users can login with "username", but
     * the login itself is stored as "username@authSuffix"
     *
     * @var string
     */
    protected $authSuffix = '';

    /**
     * Enable Login via Social Networks
     *
     * @var array()
     */
    protected $enableLogins = ['facebook','xing','linkedin','google','github'];

    /**
     * Enable Registration
     *
     * @var bool
     */
    protected $enableRegistration = true;

    /**
     * Notify via email user registration?
     *
     * @see ::notificationEmail
     * @var bool
     */
    protected $notifyOnRegistration = false;

    /**
     * Email to send notifications to.
     *
     * @var string
     */
    protected $notificationEmail;

    /**
     * Language in which to send the notifications.
     *
     * @var string
     */
    protected $notificationLanguage = 'en';

    /**
     * Enable to reset the password
     *
     * @var bool
     */
    protected $enableResetPassword = true;


    /**
     * Sets the "role " option
     *
     * @param $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Gets the "role" option
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Gets email address which is used in FROM header of system mails
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * Sets email address which is used in FROM header of system mails
     *
     * @param $fromEmail
     * @return $this
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * Gets the From: Name of the mail header
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Sets the From: of the mail header
     *
     * @param $fromName
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }


    /**
     * Sets the Mail Subject of the registration Mail
     *
     * @param $mailSubjectRegistration
     * @return $this
     */
    public function setMailSubjectRegistration($mailSubjectRegistration)
    {
        $this->mailSubjectRegistration = $mailSubjectRegistration;
        return $this;
    }

    /**
     * Gets the Mail Subject of the registration Mail
     *
     * @return string
     */
    public function getMailSubjectRegistration()
    {
        return $this->mailSubjectRegistration;
    }

    /**
     * @param $suffix
     * @return $this
     */
    public function setAuthSuffix($suffix)
    {
        $this->authSuffix = $suffix;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthSuffix()
    {
        return $this->authSuffix;
    }

    /**
     * @param $enableLogins
     * @return $this
     */
    public function setEnableLogins($enableLogins)
    {
        $this->enableLogins = $enableLogins;
        return $this;
    }

    /**
     * @return array()
     */
    public function getEnableLogins()
    {
        return $this->enableLogins;
    }

    /**
     * @param $enableRegistration
     * @return $this
     */
    public function setEnableRegistration($enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * @param string $notificationEmail
     *
     * @return self
     * @since 0.30
     */
    public function setNotificationEmail($notificationEmail)
    {
        $this->notificationEmail = $notificationEmail;

        return $this;
    }

    /**
     * @return string
     * @since 0.30
     */
    public function getNotificationEmail()
    {
        return $this->notificationEmail;
    }

    /**
     * @param boolean $notifyOnRegistration
     *
     * @return self
     * @since 0.30
     */
    public function setNotifyOnRegistration($notifyOnRegistration)
    {
        $this->notifyOnRegistration = $notifyOnRegistration;

        return $this;
    }

    /**
     * @return boolean
     * @since 0.30
     */
    public function getNotifyOnRegistration()
    {
        return $this->notifyOnRegistration;
    }

    /**
     * @internal
     *      Convinience method.
     *
     * @return string
     * @since 0,30
     */
    public function notifyOnRegistration()
    {
        return $this->getNotificationEmail();
    }

    /**
     * @param string $notificationLanguage
     *
     * @return self
     * @since 0.30
     */
    public function setNotificationLanguage($notificationLanguage)
    {
        $this->notificationLanguage = $notificationLanguage;

        return $this;
    }

    /**
     * @return string
     * @since 0.30
     */
    public function getNotificationLanguage()
    {
        return $this->notificationLanguage;
    }



    /**
     * @param $enableResetPassword
     * @return $this
     */
    public function setEnableResetPassword($enableResetPassword)
    {
        $this->enableResetPassword = $enableResetPassword;
        return $this;
    }

    /**
     * @return bool
     */
    public function getEnableResetPassword()
    {
        return $this->enableResetPassword;
    }
}
