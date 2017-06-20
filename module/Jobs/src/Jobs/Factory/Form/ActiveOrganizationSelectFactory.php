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
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the ActiveOrganization select box
 *
 * This creates an {@link \Jobs\Form\OrganizationSelect} with all organizations that are
 * currently associated to at least one "active" job entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.23
 * @since 0.30 - refactored to implement lazy loading organization list.
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
        /* @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');
        $query   = $request->getQuery();
        $initialId = $query->get('companyId');

        $organizations = [];

        if ($initialId) {
            /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
             * @var $repository \Organizations\Repository\Organization */
            $repositories   = $container->get('repositories');
            $repository = $repositories->get('Organizations');
            $organization  = $repository->find($initialId);
            $organizations[] = $organization;
        }

        $select         = new OrganizationSelect();

        $select->setSelectableOrganizations($organizations);
        $select->setAttribute('data-ajax', '?ajax=jobs.admin.activeorganizations');

        return $select;

    }
}
