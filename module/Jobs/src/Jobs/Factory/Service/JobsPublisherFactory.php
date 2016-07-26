<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Service;

use Core\Factory\Service\RestClientFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JobsPublisherFactory
 * @package Jobs\Factory\Service
 */
class JobsPublisherFactory extends RestClientFactory
{
    protected $config;
    /**
     * @var ServiceLocatorInterface $serviceLocator
     */
    protected $serviceLocator;



    protected function getUri()
    {
        $config = $this->getConfig();
        if (!array_key_exists('scheme', $config)) {
            throw new \RuntimeException('scheme is missing', 500);
        }
        if (!array_key_exists('host', $config)) {
            throw new \RuntimeException('host is missing', 500);
        }
        if (!array_key_exists('path', $config)) {
            throw new \RuntimeException('path is missing', 500);
        }
        return $config['scheme'] . '://' . $config['host'] . '/' . $config['path'];
    }

    /**
     *
     * 'PHP_AUTH_USER' => $user,
     * 'PHP_AUTH_PW' => $password,
     *
     * @return mixed
     */
    protected function getConfig()
    {
        $jobsOptions = $this->serviceLocator->get('Jobs/Options');

        if (!isset($this->multipostingTarget) && isset($jobsOptions->multipostingTargetUri)) {
            // The Uri has this Form
            // scheme://user:pass@host/path
            $parseResult = parse_url($jobsOptions->multipostingTargetUri);
            $this->config = $parseResult;
        }
        return $this->config;
    }
}
