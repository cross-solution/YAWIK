<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Controller;

use Auth\AuthenticationService;
use Auth\Controller\PasswordController;
use Auth\Form;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PasswordControllerFactory implements FactoryInterface
{
    /**
     * Create a PasswordController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return PasswordController
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var AuthenticationService $authenticationService
         * @var Form\UserPassword     $form
         * @var RepositoryService     $repositoryService
         */
        $authenticationService = $container->get('AuthenticationService');
        $form = $container->get('forms')->get('user-password');
        $repositoryService = $container->get('repositories');

        return new PasswordController($authenticationService, $form, $repositoryService);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return PasswordController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, PasswordController::class);
    }
}
