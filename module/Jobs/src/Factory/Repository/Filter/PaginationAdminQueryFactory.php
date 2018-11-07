<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Repository\Filter;

use Auth\AuthenticationService;
use Interop\Container\ContainerInterface;
use Jobs\Repository\Filter\PaginationAdminQuery;
use Zend\ServiceManager\Factory\FactoryInterface;

class PaginationAdminQueryFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var AuthenticationService $auth */
        $auth = $container->get('AuthenticationService');
        $acl = $container->get('Acl');
        $filter = new PaginationAdminQuery($auth, $acl);
        return $filter;

    }
}
