<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Listener;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating exception strategies
 */
class ExceptionStrategyFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        switch ($requestedName)
        {
            case 'UnauthorizedAccessListener':
                $listener = new \Auth\Listener\UnauthorizedAccessListener();
                break;

            case 'DeactivatedUserListener':
                $listener = new \Auth\Listener\DeactivatedUserListener();
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unknown service %s', $requestedName));
                break;
        }

        $config   = $container->get('Config');

        if (isset($config['view_manager'])) {
            if (isset($config['view_manager']['display_exceptions'])) {
                $listener->setDisplayExceptions($config['view_manager']['display_exceptions']);
            }
            if (isset($config['view_manager']['unauthorized_template'])) {
                $listener->setExceptionTemplate($config['view_manager']['unauthorized_template']);
            }
        }
        return $listener;
    }

    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null)
    {
        return $this($serviceLocator, $canonicalName);
    }
}
