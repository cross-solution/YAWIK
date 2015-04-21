<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Controller\SLFactory;

use Auth\AuthenticationService;
use Auth\Controller\PasswordController;
use Auth\Form;
use Core\Repository\RepositoryService;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PasswordControllerSLFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PasswordController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ControllerManager $serviceLocator */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /**
         * @var AuthenticationService $authenticationService
         * @var Form\UserPassword     $form
         * @var RepositoryService     $repositoryService
         */
        $authenticationService = $serviceLocator->get('AuthenticationService');
        $form = $serviceLocator->get('forms')->get('user-password');
        $repositoryService = $serviceLocator->get('repositories');

//        var_dump($form); exit;

        return new PasswordController($authenticationService, $form, $repositoryService);
    }
}