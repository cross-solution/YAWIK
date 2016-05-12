<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Controller;

use Auth\Controller\UsersController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UsersControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UsersController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator ServiceLocatorInterface */
        $serviceLocator = $serviceLocator->getServiceLocator();

        /* @var $users \Auth\Repository\User */
        $users = $serviceLocator->get('repositories')->get('Auth/User');

        return new UsersController($users);
    }
}
