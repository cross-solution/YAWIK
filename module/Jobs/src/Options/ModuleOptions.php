<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
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
class ModuleOptions extends AbstractOptions
{

    /**
     * Send an approval Mail to this Email Address, if a new job is posted.
     *
     * @var string $multipostingApprovalMail
     */
    protected $multipostingApprovalMail;

    /**
     * Send a Rest Request to this target on status changes of a job opening.
     * The URI can contain Username/Password
     *
     * E.g.: http://user:pass@host/location?query
     *
     * @var string $multipostingTargetUri
     */
    protected $multipostingTargetUri = 'http://user:pass@host/location?query';

    /**
     * The default Logo is shown in a job opening and in the application form
     *
     * @var string $defaultLogo
     */
    protected $defaultLogo="modules/Jobs/images/yawik-small.jpg";

    /**
     * Maximum size in bytes of a company Logo. Default 200k
     *
     * @var int $companyLogoMaxSize
     */
    protected $companyLogoMaxSize=200000;

    /**
     * Allowed Mime-Types for company Logos
     *
     * @var array
     */
    protected $companyLogoMimeType=array("image");


    /**
     * Gets the email address to which approval mails are sent.
     *
     * @return string
     */
    public function getMultipostingApprovalMail()
    {
        return $this->multipostingApprovalMail;
    }
    /**
     * Sets the email address to which approval mails are sent
     *
     * @param string $multipostingApprovalMail
     * @return ModuleOptions
     */
    public function setMultipostingApprovalMail($multipostingApprovalMail)
    {
        $this->multipostingApprovalMail = $multipostingApprovalMail;
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
     * @param string $logo
     * @return ModuleOptions
     */
    public function setDefaultLogo($logo)
    {
        $this->defaultLogo = $logo;
        return $this;
    }

    /**
     * Gets the maximum size in bytes of a company Logo
     *
     * @return int
     */
    public function getCompanyLogoMaxSize()
    {
        return $this->companyLogoMaxSize;
    }
    /**
     * Sets Maximum size in bytes of a company Logo
     *
     * @param int $size
     * @return ModuleOptions
     */
    public function setCompanyLogoMaxSize($size)
    {
        $this->companyLogoMaxSize = $size;
        return $this;
    }

    /**
     * Gets the allowed Mime-Type of a company logo
     *
     * @return array
     */
    public function getCompanyLogoMimeType()
    {
        return $this->companyLogoMimeType;
    }

    /**
     * Sets the allowed Mime-Types of a company logo
     *
     * @param array $mime
     * @return ModuleOptions
     */
    public function setCompanyLogoMimeType($mime)
    {
        $this->companyLogoMimeType = $mime;
        return $this;
    }
}
