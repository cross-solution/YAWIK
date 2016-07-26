<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Paginator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ServiceLocator for Paginators
 *
 * Class PaginatorServiceFactory
 * @package Core\Paginator
 */
class PaginatorServiceFactory implements FactoryInterface
{

    /** (non-PHPdoc)
    * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $configArray = $serviceLocator->get('Config');
        $configArray = isset($configArray['paginator_manager']) ? $configArray['paginator_manager'] : array();
        $config      = new PaginatorServiceConfig($configArray);

        $service   = new PaginatorService($serviceLocator, $config);

        return $service;
    }
}
