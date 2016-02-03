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
     * Creates the organization select box.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
         * @var $jobsRepository \Jobs\Repository\Job
         */
        $services       = $serviceLocator->getServiceLocator();
        $repositories   = $services->get('repositories');
        $jobsRepository = $repositories->get('Jobs');
        $organizations  = $jobsRepository->findActiveOrganizations();
        $select         = new OrganizationSelect();

        $select->setSelectableOrganizations($organizations);

        return $select;
    }
}
