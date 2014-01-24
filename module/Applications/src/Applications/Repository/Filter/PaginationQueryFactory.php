<?php

namespace Applications\Repository\Filter;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class PaginationQueryFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $auth  = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new PaginationQuery($auth);
        return $filter;
    }
}