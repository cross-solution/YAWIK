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
        if (!array_key_exists('uri', $config)) {
            throw new \RuntimeException('uri for Rest-Server YAWIK is missing', 500);
        }
        return $config['uri'];
    }

    protected function getConfig() {
        if (!isset($this->config)) {
            $config = $this->serviceLocator->get('Config');
            if (!array_key_exists('multiposting', $config)) {
                throw new \RuntimeException('configuration for multiposting is missing', 500);
            }
            if (!array_key_exists('target', $config['multiposting'])) {
                throw new \RuntimeException('target for multiposting is missing', 500);
            }
            if (!array_key_exists('restServer', $config['multiposting']['target'])) {
                throw new \RuntimeException('configuration restServer for multiposting.target is missing', 500);
            }
            $this->config = $config['multiposting']['target']['restServer'];
        }
        return $this->config;
    }
}
