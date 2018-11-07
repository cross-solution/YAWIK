<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helper factories*/
namespace Core\View\Helper\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\View\Helper\Params;

/**
 * Factors a params helper instance.
 * @see \Core\View\Helper\Params
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ParamsHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $event = $container->get('Application')->getMvcEvent();
        $helper = new Params($event);
        return $helper;
    }
    
    /**
     * Creates an instance of \Core\View\Helper\Params
     *
     * - injects the MvcEvent instance
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Core\View\Helper\Params
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Params::class);
    }
}
