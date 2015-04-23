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
 * Class ListenerPublisherOptions
 * @package YawikXingVendorApi\Options
 */
class ControllerIndexOptions extends AbstractOptions {

    /**
     * the preview indicates, that a job is not shown
     * @var bool
     */
    protected $fromEmail = 'contact@yawik.org';

    /**
     * if no organizationId was set, return the id of the Sandbox
     * Jobs in the sandbox can not be inspected in the backend, their
     * only function is to get a negative or positive answer from the transmit
     * @var string
     */
    protected $fromName = 'YAWIK';

    /**
     * @var string
     */
    protected $role = 'recruiter';

    /**
     * @var
     */
    protected $mailName = 'Yawik';

    /**
     * @var
     */
    protected $mailFrom = 'demo@yawik.org';

    /**
     * @var
     */
    protected $mailSubject = 'Welcome to YAWIK';

    /**
     * @var
     */
    protected $authSuffix = 'yawik';

    /**
     * @param $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param $mailName
     * @return $this
     */
    public function setMailName($mailName)
    {
        $this->mailName = $mailName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMailName()
    {
        return $this->mailName;
    }

    /**
     * @param $mail
     * @return $this
     */
    public function setMailFrom($mail)
    {
        $this->fromEmail = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setMailSubject($subject)
    {
        $this->mailSubject = $subject;
        return $this;
    }

    /**
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
     *
     */
    public function getEmail()
    {
        return $this->fromEmail;
    }

    /**
     *
     */
    public function setEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     *
     */
    public function getName()
    {
        return $this->fromName;
    }

    /**
     *
     */
    public function setName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

}