<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Organizations\Factory\Controller;

use Interop\Container\ContainerInterface;
use Organizations\Controller\TypeAHeadController;
use Organizations\Repository;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TypeAHeadControllerFactory implements FactoryInterface
{
    /**
     * Create a TypeAHeadController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return TypeAHeadController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var $organizationRepository Repository\Organization
         */
        $organizationRepository = $container->get('repositories')->get('Organizations/Organization');

        return new TypeAHeadController($organizationRepository);
    }

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
        return $this($serviceLocator->getServiceLocator(), TypeAHeadController::class);
    }
}
