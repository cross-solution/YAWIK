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

use Interop\Container\ContainerInterface;
use Jobs\Listener\Events\JobEvent;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factors the JobEventManager which is used to trigger Job Events.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
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
        $events->setEventPrototype(new JobEvent());
        $events->setIdentifiers($this->identifiers);

        return $events;
    }
}
