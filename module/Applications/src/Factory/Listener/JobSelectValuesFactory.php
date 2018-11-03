<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Factory\Listener;

use Applications\Listener\JobSelectValues;
use Applications\Paginator\JobSelectPaginator;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \Applications\Listener\JobSelectValues
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelectValuesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return JobSelectValues
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $paginators = $container->get('Core/PaginatorService');
        $paginator  = $paginators->get(JobSelectPaginator::class);

        $listener   = new JobSelectValues($paginator);

        return $listener;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return JobSelectValues
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, JobSelectValues::class);
    }
}
