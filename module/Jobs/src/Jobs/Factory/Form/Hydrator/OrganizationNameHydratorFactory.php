<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
  */

namespace Jobs\Factory\Form\Hydrator;

use Interop\Container\ContainerInterface;
use Jobs\Form\Hydrator\OrganizationNameHydrator;
use Organizations\Repository\Organization;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class OrganizationNameHydratorFactory
 * @package Jobs\Factory\Form\Hydrator
 */
class OrganizationNameHydratorFactory implements FactoryInterface
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
        /* @var $hydrator Organization */
        $organizationRepository = $container->get('repositories')->get('Organizations/Organization');

        $hydrator = new OrganizationNameHydrator($organizationRepository);

        return $hydrator;
    }


    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OrganizationNameHydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, OrganizationNameHydrator::class);
    }
}
