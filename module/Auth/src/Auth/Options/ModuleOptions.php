<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * defines AbstractOptions of the Auth Module
 */
class ModuleOptions extends AbstractOptions {

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
     * Use the siteName of the Core Options
     *
     * @deprecated
     * @var string
     */
    protected $siteName = '';

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
     * @deprecated
     * @param string $siteName
     * @return $this
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
        return $this;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }
}