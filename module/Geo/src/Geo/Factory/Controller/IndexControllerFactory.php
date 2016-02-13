<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Geo\Factory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Geo\Controller\IndexController;
use Geo\Options\ModuleOptions;


class IndexControllerFactory implements FactoryInterface
{
    /**
     * Create controller
     *
     * @param ServiceLocatorInterface $controllerManager
     *
     * @return IndexController
     */
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        /* @var \Zend\Mvc\Controller\PluginManager $controllerManager */
        $serviceLocator = $controllerManager->getServiceLocator();

        /* @var ModuleOptions $options  */
        $options = $serviceLocator->get('Geo/Options');

        $controller = new IndexController($options);
        return $controller;
    }
}