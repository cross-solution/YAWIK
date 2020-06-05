<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** OrganizationEntityHydrator.php */
namespace Organizations\Entity\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Organizations\Entity\Hydrator\Strategy\HttploadStrategy;
use Organizations\Entity\Hydrator\Strategy\OrganizationNameStrategy;

class OrganizationHydratorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositoryManager = $container->get('repositories');
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
