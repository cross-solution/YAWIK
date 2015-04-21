<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Factory\Service;

use Core\Service\RestClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class RestClientFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Create the settings service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return RestClient
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $service = new RestClient($this->getUri(), $this->getConfig());
        return $service;
    }

    /**
     * @return mixed
     */
    abstract protected function getUri();

    /**
     * @return mixed
     */
    abstract protected function getConfig();
}