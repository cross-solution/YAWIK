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

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Repository\RepositoryService;
use Jobs\Controller\Plugin\InitializeJob;

class InitializeJobFactory implements FactoryInterface
{

    /**
     * Create an InitializeJob
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return InitializeJob
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $repositories RepositoryService */
        $repositories = $container->get('repositories');
        /* @var \Auth\AuthenticationService */
        $auth = $container->get('AuthenticationService');
        /* @var \Acl\Controller\Plugin\Acl */
        $acl = $container->get('ControllerPluginManager')->get('acl');

        $plugin = new InitializeJob($repositories, $auth, $acl);
        return $plugin;
    }
}
