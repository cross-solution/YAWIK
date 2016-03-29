<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Jobs\Factory\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\RepositoryService;
use Jobs\Controller\Plugin\InitializeJob;

class InitializeJobFactory implements FactoryInterface
{
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

        $plugin = new InitializeJob($repositories, $auth, $acl);
        return $plugin;
    }
}
