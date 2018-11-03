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
use Jobs\Form\HiringOrganizationSelect;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for the HiringOrganization select box
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class HiringOrganizationSelectFactory implements FactoryInterface
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
        /* @var $headscript     \Zend\View\Helper\HeadScript
         * @var $user           \Auth\Entity\User
         * @var $organization   \Organizations\Entity\OrganizationInterface | \Organizations\Entity\OrganizationReferenceInterface
         */
        $user         = $container->get('AuthenticationService')->getUser();
        $select       = new HiringOrganizationSelect();
        $organizationReference = $user->getOrganization();

        if ($organizationReference->hasAssociation()) {
            $organizations = $organizationReference->getHiringOrganizations()->toArray();
            $organization = $organizationReference->getOrganization();
            if (!$organization->isDraft()) {
                array_unshift($organizations, $organization);
            }
            $select->setSelectableOrganizations(
                $organizations, /* addEmptyOption */
                                                false
            );
        }

        return $select;
    }
}
