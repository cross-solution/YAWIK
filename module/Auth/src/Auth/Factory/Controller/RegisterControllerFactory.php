<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Controller;

use Auth\Controller\RegisterController;
use Auth\Form;
use Auth\Service;
use Auth\Options\ModuleOptions;
use Interop\Container\ContainerInterface;
use Zend\Log\LoggerInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterControllerFactory implements FactoryInterface
{
    /**
     * Create a RegisterController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return RegisterController
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var $form    Form\Register
         * @var $service Service\Register
         * @var $logger  LoggerInterface
         * @var $options  ModuleOptions
         */
        $formElementManager = $container->get('FormElementManager');
        $form = $formElementManager->get('Auth\Form\Register');

        $service = $container->get('Auth\Service\Register');
        $logger = $container->get('Core/Log');
        $options = $container->get('Auth/Options');

        return new RegisterController($form, $service, $logger, $options);

    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RegisterController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, RegisterController::class);
    }
}
