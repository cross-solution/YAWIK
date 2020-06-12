<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Factory\Listener;

use Applications\Listener\EventApplicationCreated;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 *
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class EventApplicationCreatedFactory implements FactoryInterface
{
    /**
     * Create a EventApplicationCreated listener
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return EventApplicationCreated
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mailService     = $container->get('Core/MailService');
        $listener        = new EventApplicationCreated($mailService);
        return $listener;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, EventApplicationCreated::class);
    }
}
