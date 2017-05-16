<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Service;

use Auth\Repository;
use Auth\Service\ForgotPassword;
use Auth\Service\UserUniqueTokenGenerator;
use Core\Controller\Plugin;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ForgotPasswordFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ForgotPassword
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /**
         * @var Repository\User          $userRepository
         * @var UserUniqueTokenGenerator $tokenGenerator
         */
        $userRepository = $container->get('repositories')->get('Auth/User');
        $tokenGenerator = $container->get('Auth\Service\UserUniqueTokenGenerator');
        $loginFilter = $container->get('Auth\LoginFilter');
        $config = $container->get('Auth/Options');

        $service = new ForgotPassword($userRepository, $tokenGenerator, $loginFilter, $config);

        $events = $container->get('Auth/Events');
        $service->setEventManager($events);

        return $service;
    }
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ForgotPassword
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ForgotPassword::class);
    }
}
