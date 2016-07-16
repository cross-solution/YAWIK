<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Options;


use Zend\Stdlib\AbstractOptions;

/**
 * Provide available options for Solr Module
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Options
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @var string
     */
    protected $hostname = 'localhost';

    /**
     * @var integer
     */
    protected $port = 80;

    /**
     * @var string
     */
    protected $path = '/solr';

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $jobsPath = '/solr/YawikJobs';

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param boolean $secure
     * @return ModuleOptions
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     * @return ModuleOptions
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return ModuleOptions
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return ModuleOptions
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return ModuleOptions
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return ModuleOptions
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getJobsPath()
    {
        return $this->jobsPath;
    }

    /**
     * @param string $jobsPath
     * @return ModuleOptions
     */
    public function setJobsPath($jobsPath)
    {
        $this->jobsPath = $jobsPath;

        return $this;
    }
}