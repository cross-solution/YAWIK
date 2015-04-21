<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Organizations\Factory\Controller;

use Organizations\Controller\TypeAHeadController;
use Organizations\Repository;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TypeAHeadControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TypeAHeadController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var $organizationRepository Repository\Organization
         */
        $organizationRepository = $serviceLocator->get('repositories')->get('Organizations/Organization');

        return new TypeAHeadController($organizationRepository);
    }
}