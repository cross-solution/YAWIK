<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
  */

namespace Jobs\Factory\Form\Hydrator;

use Core\Entity\Hydrator\MappingEntityHydrator;
use Interop\Container\ContainerInterface;
use Jobs\Form\Hydrator\Strategy\JobManagerStrategy;
use Jobs\Form\Hydrator\Strategy\OrganizationNameStrategy;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
        $organizationRepository = $container->get('repositories')->get('Organizations/Organization');
        $organizationNameStrategy = new OrganizationNameStrategy($organizationRepository);

        $hydrator = new MappingEntityHydrator([
            'organization' => 'companyId',
            'metaData'     => 'managers',
        ]);
        $hydrator->addStrategy('companyId', $organizationNameStrategy);
        $hydrator->addStrategy('managers', new JobManagerStrategy());

        return $hydrator;
    }
}
