<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Factory\Paginator;

use Applications\Paginator\JobSelectPaginator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \Applications\Paginator\JobSelectPaginator
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelectPaginatorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return JobSelectPaginator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');
        $repository   = $repositories->get('Jobs');

        $paginator = new JobSelectPaginator($repository);

        return $paginator;
    }

    /**
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     *
     * @return JobSelectPaginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, JobSelectPaginator::class);
    }
}
