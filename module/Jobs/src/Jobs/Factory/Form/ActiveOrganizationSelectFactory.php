<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Jobs\Factory\Form;

use Interop\Container\ContainerInterface;
use Jobs\Form\OrganizationSelect;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the ActiveOrganization select box
 *
 * This creates an {@link \Jobs\Form\OrganizationSelect} with all organizations that are
 * currently associated to at least one "active" job entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.23
 */
class ActiveOrganizationSelectFactory implements FactoryInterface
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
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
         * @var $jobsRepository \Jobs\Repository\Job
         */
        $repositories   = $container->get('repositories');
        $jobsRepository = $repositories->get('Jobs');
        $organizations  = $jobsRepository->findActiveOrganizations();
        $select         = new OrganizationSelect();

        $select->setSelectableOrganizations($organizations);

        return $select;

    }

    /**
     * Creates the organization select box.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), OrganizationSelect::class);
    }
}
