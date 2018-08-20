<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Factory\Controller;

use Auth\Controller\RemoveController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class RemoveControllerFactory implements FactoryInterface
{
    /**
     * Create a RemoveController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return RemoveController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $dependencyManager = $container->get('Auth/Dependency/Manager');
        $authService = $container->get('AuthenticationService');
        $userRepository = $container->get('repositories')->get('Auth/User');

        return new RemoveController($dependencyManager, $authService, $userRepository);
    }
}
