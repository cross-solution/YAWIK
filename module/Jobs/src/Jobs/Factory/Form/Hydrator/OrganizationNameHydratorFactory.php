<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
  */

namespace Jobs\Factory\Form\Hydrator;

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
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OrganizationNameHydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $hydrator Organization */
        $organizationRepository = $serviceLocator->get('repositories')->get('Organizations/Organization');

        $hydrator = new OrganizationNameHydrator($organizationRepository);

        return $hydrator;
    }
}