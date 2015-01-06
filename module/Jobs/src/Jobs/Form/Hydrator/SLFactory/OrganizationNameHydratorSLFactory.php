<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
  */

namespace Jobs\Form\Hydrator\SLFactory;

use Jobs\Form\Hydrator\OrganizationNameHydrator;
use Organizations\Repository\Organization;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class OrganizationNameHydratorSLFactory implements FactoryInterface
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
        /**
         * @var $hydrator Organization
         */
        $organizationRepository = $serviceLocator->get('repositories')->get('Organizations/Organization');

        $hydrator = new OrganizationNameHydrator($organizationRepository);

        return $hydrator;
    }
}