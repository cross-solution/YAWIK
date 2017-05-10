<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory;

use Core\EventManager\EventManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factors the JobEventManager which is used to trigger Job Events.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class JobEventManagerFactory implements FactoryInterface
{
    protected $identifiers = array(
        'Jobs',
        'Jobs/Events',
    );

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
        /* @var $events \Zend\EventManager\EventManagerInterface */
        $events = $container->get('EventManager');

        $events->setEventClass('\Jobs\Listener\Events\JobEvent');
        $events->setIdentifiers($this->identifiers);

        return $events;
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
        return $this($serviceLocator, EventMfutanager::class);
    }
}
