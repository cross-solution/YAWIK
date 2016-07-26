<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Jobs\Factory\Controller;

use Jobs\Controller\ManageController;
use Core\Repository\RepositoryService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ManageControllerFactory implements FactoryInterface
{
    /**
     * Injects all needed services into the ManageController
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ManageController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();
        $auth = $serviceLocator->get('AuthenticationService');
        /* @var RepositoryService     $repositoryService */
        $repositoryService =    $serviceLocator->get('repositories');

        $translator =    $serviceLocator->get('translator');
        return new ManageController($auth, $repositoryService, $translator);
    }
}
