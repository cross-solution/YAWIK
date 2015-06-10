<?php

namespace Jobs\Repository\Filter;

use Auth\AuthenticationService;
use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

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
        /* @var AuthenticationService $auth */
        $services = $serviceLocator->getServiceLocator();
        $auth                   = $services->get('AuthenticationService');
        $acl = $services->get('Acl');

        $filter = new PaginationQuery($auth, $acl);
        return $filter;
    }
}