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
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UsersControllerFactory implements FactoryInterface
{
    /**
     * Create a UsersController controller
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UsersController
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $users \Auth\Repository\User */
        $users = $container->get('repositories')->get('Auth/User');
		$formManager = $container->get('forms');
		$viewHelper = $container->get('ViewHelperManager');
        return new UsersController($users,$formManager,$viewHelper);
    }
}
