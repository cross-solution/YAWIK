<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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



    protected function getUri() {
        $config = $this->getConfig();
        if (!array_key_exists('host', $config)) {
            throw new \RuntimeException('host is missing', 500);
        }
        if (!array_key_exists('location', $config)) {
            throw new \RuntimeException('location is missing', 500);
        }
        return $config['host'] . '/' . $config['location'];
    }

    protected function getConfig() {
        $jobsOptions = $this->serviceLocator->get('Jobs/Options');

        if (!isset($this->multipostingTarget) && isset($jobsOptions->multipostingTargetUri)) {
            // The Uri has this Form
            // http://user:pass@host/location?query
            if (preg_match('/^https*:\/\/([^:]*):([^@]*)@([^\/]*)\/([^\?]*)\??(.*)$/i', $jobsOptions->multipostingTargetUri, $elements)) {
                $user = $elements[1];
                $password = $elements[2];
                $host = $elements[3];
                $location = $elements[4];
                $query = $elements[5];


                /*
                if (!array_key_exists('multiposting', $config)) {
                    throw new \RuntimeException('configuration for multiposting is missing', 500);
                }
                if (!array_key_exists('target', $config['multiposting'])) {
                    throw new \RuntimeException('target for multiposting is missing', 500);
                }
                if (!array_key_exists('restServer', $config['multiposting']['target'])) {
                    throw new \RuntimeException('configuration restServer for multiposting.target is missing', 500);
                }
                */
                $this->config = array(
                    'PHP_AUTH_USER' => $user,
                    'PHP_AUTH_PW' => $password,
                    'host' => $host,
                    'location'=> $location,
                    'query' => $query
                );
            }
        }
        return $this->config;
    }
}
