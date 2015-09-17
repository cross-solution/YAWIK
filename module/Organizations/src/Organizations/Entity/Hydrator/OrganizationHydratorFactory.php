<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** OrganizationEntityHydrator.php */
namespace Organizations\Entity\Hydrator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\Entity\Hydrator\Strategy\HttploadStrategy;
use Organizations\Entity\Hydrator\Strategy\OrganizationNameStrategy;

class OrganizationHydratorFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repositoryManager = $serviceLocator->getServiceLocator()->get('repositories');
        $repOrganization = $repositoryManager->get('Organizations/Organization');
        $repOrganizationName = $repositoryManager->get('Organizations/OrganizationName');
        $repOrganizationImage = $repositoryManager->get('Organizations/OrganizationImage');
        $object = new OrganizationHydrator($repOrganization, $repOrganizationName, $repOrganizationImage);
        
        // injecting the strategies
        $httpload = new HttploadStrategy($repOrganizationImage);
        $organizationName = new OrganizationNameStrategy($repOrganizationName);
        $object->addStrategy('image', $httpload);
        $object->addStrategy('organizationName', $organizationName);
        return $object;
    }
}
