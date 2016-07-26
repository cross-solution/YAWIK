<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Organizations\Factory\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\RepositoryService;
use Organizations\Controller\Plugin\GetOrganizationHandler;

class GetOrganizationHandlerFactory implements FactoryInterface {
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\Mvc\Controller\PluginManager */
        $services = $serviceLocator->getServiceLocator();
        /* @var $repositories RepositoryService */
        $repositories = $services->get('repositories');
        /* @var \Auth\AuthenticationService */
        $auth = $services->get('AuthenticationService');
        /* @var \Acl\Controller\Plugin\Acl */
        $acl = $serviceLocator->get('acl');

        $plugin = new GetOrganizationHandler($repositories, $auth, $acl);
        return $plugin;
    }
}
