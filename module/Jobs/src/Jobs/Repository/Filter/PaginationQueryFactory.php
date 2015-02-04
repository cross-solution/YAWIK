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
        /* @var AuthenticationService $auth */
        $auth                   = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new PaginationQuery($auth);
        return $filter;
    }
}