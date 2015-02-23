<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace Jobs\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * Default options of the Jobs Module
 *
 * @package Jobs\Options
 */
class ModuleOptions extends AbstractOptions {

    /**
     * Send an approval Mail to this Email Address, if a new job is posted.
     *
     * @var int $multipostingApprovalMail
     */
    protected $multipostingApprovalMail;

    /**
     * @var string $multipostingTargetUri
     */
    protected $multipostingTargetUri;

    /**
     * @var string $multipostingTargetUser
     */
    protected $multipostingTargetUser;

    /**
     * @var string $multipostingTargetPassword
     */
    protected $multipostingTargetPassword;

    /**
     * The default Logo is shown in a job opening and in the application form
     *
     * @var string $defaultLogo
     */
    protected $defaultLogo="/Jobs/images/yawik-small.jpg";

    /**
     * Gets the email address to which approval mails are sent
     *
     * @return int
     */
    public function getMultipostingApprovalMail()
    {
        if (null == $this->multipostingApprovalMail){

        }
        return $this->multipostingApprovalMail;
    }
    /**
     * Sets the email address to which approval mails are sent
     *
     * @param int $size
     * @return ModuleOptions
     */
    public function setMultipostingApprovalMail($size)
    {
        $this->multipostingApprovalMail = $size;
        return $this;
    }

    /**
     * Gets a target where to send REST requests after a job opening was accepted
     *
     * @return string
     */
    public function getMultipostingTargetUri()
    {
        return $this->multipostingTargetUri;
    }

    /**
     * Sets a target where to send REST requests after a job opening was accepted
     *
     * @param string $uri
     * @return ModuleOptions
     */
    public function setMultipostingTargetUri($uri)
    {
        $this->multipostingTargetUri = $uri;
        return $this;
    }

    /**
     * Gets the username for sending a Rest request, after a job opening was accepted
     *
     * @return string
     */
    public function getMultipostingTargetUser()
    {
        return $this->multipostingTargetUser;
    }
    /**
     * Sets the username for sending a Rest request, after a job opening was accepted
     *
     * @param string $username
     * @return ModuleOptions
     */
    public function setMultipostingTargetUser($username)
    {
        $this->multipostingTargetUser = $username;
        return $this;
    }

    /**
     * Gets the password for sending a Rest request, after a job opening was accepted
     *
     * @return string
     */
    public function getMultipostingTargetPassword()
    {
        return $this->multipostingTargetPassword;
    }
    /**
     * Sets the password for sending a Rest request, after a job opening was accepted
     *
     * @param string $password
     * @return ModuleOptions
     */
    public function setMultipostingTargetPassword($password)
    {
        $this->multipostingTargetPassword = $password;
        return $this;
    }

    /**
     * Gets the default logo of a job opening/application formular
     *
     * @return string
     */
    public function getDefaultLogo()
    {
        return $this->defaultLogo;
    }
    /**
     * Sets the default logo of a job opening/application formular
     *
     * @param string $mime
     * @return ModuleOptions
     */
    public function setDefaultLogo($logo)
    {
        $this->defaultLogo = $logo;
        return $this;
    }

}