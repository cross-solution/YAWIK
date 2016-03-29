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

use Jobs\Form\HiringOrganizationSelect;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the HiringOrganization select box
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class HiringOrganizationSelectFactory implements FactoryInterface
{
    /**
     * Creates the hiring organization select box.
     *
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager
         * @var $headscript     \Zend\View\Helper\HeadScript
         * @var $user           \Auth\Entity\User
         * @var $organization   \Organizations\Entity\OrganizationInterface | \Organizations\Entity\OrganizationReferenceInterface
         */
        $services     = $serviceLocator->getServiceLocator();
        $user         = $services->get('AuthenticationService')->getUser();
        $select       = new HiringOrganizationSelect();
        $organization = $user->getOrganization();

        if ($organization->hasAssociation()) {
            $organizations = $organization->getHiringOrganizations()->toArray();
            array_unshift($organizations, $organization->getOrganization());
            $select->setSelectableOrganizations($organizations, /* addEmptyOption */
                                                false
            );
        }

        return $select;
    }
}
