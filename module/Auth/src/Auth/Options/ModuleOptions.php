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
    protected $fromEmail = 'contact@yawik.org';

    /**
     * default name, which is used in FROM headers of system mails like "new registration", "forgot password",..
     *
     * @var string
     */
    protected $fromName = 'YAWIK';

    /**
     * default role, which is assigned to a user after registration. possible Values (user|recruiter)
     * @var string
     */
    protected $role = 'recruiter';

    /**
     * use $fromName instead
     *
     * @deprecated
     * @var string
     */
    protected $mailName = 'Yawik';

    /**
     * use $fromEmail instead
     *
     * @deprecated
     * @var string
     */
    protected $mailFrom = 'demo@yawik.org';

    /**
     *
     * @deprecated
     * @var string
     */
    protected $mailSubject = 'Welcome to YAWIK';

    /**
     * an authSuffix can be used, if you plan to connect an external system. Users can login with "username", but
     * the login itself is stored as "username@authSuffix"
     *
     * @var string
     */
    protected $authSuffix = '';

    /**
     * sets the "role " option
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
     * gets the "role" option
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @deprecated
     * @param $mailName
     * @return $this
     */
    public function setMailName($mailName)
    {
        $this->mailName = $mailName;
        return $this;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getMailName()
    {
        return $this->mailName;
    }

    /**
     * @deprecated
     * @param $mail
     * @return $this
     */
    public function setMailFrom($mail)
    {
        $this->fromEmail = $mail;
        return $this;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * @deprecated
     * @param $subject
     * @return $this
     */
    public function setMailSubject($subject)
    {
        $this->mailSubject = $subject;
        return $this;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getMailSubject()
    {
        return $this->mailSubject;
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
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param $fromEmail
     * @return $this
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param $fromName
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }
}