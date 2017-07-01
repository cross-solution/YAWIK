<?php

namespace Jobs\Factory\Repository\Filter;

use Auth\AuthenticationService;
use Interop\Container\ContainerInterface;
use Jobs\Repository\Filter\PaginationQuery;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PaginationQueryFactory
 * @package Jobs\Repository\Filter
 */
class PaginationQueryFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var AuthenticationService $auth */
        $auth = $container->get('AuthenticationService');
        $acl = $container->get('Acl');

        $filter = new PaginationQuery($auth, $acl);
        return $filter;
    }
}
