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

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Core\Repository\RepositoryService;
use Organizations\Controller\Plugin\GetOrganizationHandler;

class GetOrganizationHandlerFactory implements FactoryInterface
{

    /**
     * Create a GetOrganizationHandler controller plugin
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return GetOrganizationHandler
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $repositories RepositoryService */
        $repositories = $container->get('repositories');
        /* @var \Auth\AuthenticationService */
        $auth = $container->get('AuthenticationService');
        /* @var \Acl\Controller\Plugin\Acl */
        $acl = $container->get('ControllerPluginManager')->get('acl');

        $plugin = new GetOrganizationHandler($repositories, $auth, $acl);
        return $plugin;
    }
}
