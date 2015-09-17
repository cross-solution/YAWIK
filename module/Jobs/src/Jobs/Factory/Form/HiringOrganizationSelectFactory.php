<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
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
         * @var $organization   \Organizations\Entity\OrganizationInterface | \Organizations\Entity\OrganizationReferenceInterface */
        $services = $serviceLocator->getServiceLocator();
        $user     = $services->get('AuthenticationService')->getUser();
        $select   = new HiringOrganizationSelect();
        $helpers  = $services->get('ViewHelperManager');
        $headscript = $helpers->get('headscript');
        $basepath   = $helpers->get('basepath');
        $organization = $user->getOrganization();

        $headscript->appendFile($basepath('Jobs/js/form.hiring-organization-select.js'));

        $options = array();

        if ($organization->hasAssociation()) {
            $organizations = $organization->getHiringOrganizations()->toArray();
            array_unshift($organizations, $organization->getOrganization());

            foreach ($organizations as $org) {
                /* @var $org \Organizations\Entity\OrganizationInterface */

                $name = $org->getOrganizationName()->getName();
                $contact = $org->getContact();

                $options[$org->getId()] =  $name . '|'
                                . $contact->getCity() . '|'
                                . $contact->getStreet() . '|'
                                . $contact->getHouseNumber();
            }
        }

        $select->setAttribute('data-autoinit', 'false');
        $select->setValueOptions($options);

        return $select;
    }
}
