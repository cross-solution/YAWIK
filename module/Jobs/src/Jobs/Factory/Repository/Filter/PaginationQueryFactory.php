<?php

namespace Jobs\Factory\Repository\Filter;

use Auth\AuthenticationService;
use Jobs\Repository\Filter\PaginationQuery;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PaginationQueryFactory
 * @package Jobs\Repository\Filter
 */
class PaginationQueryFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return PaginationQuery|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $services \Zend\ServiceManager\ServiceManager */
        $services = $serviceLocator->getServiceLocator();
        /* @var AuthenticationService $auth */
        $auth = $services->get('AuthenticationService');
        $acl = $services->get('Acl');

        $filter = new PaginationQuery($auth, $acl);
        return $filter;
    }
}
