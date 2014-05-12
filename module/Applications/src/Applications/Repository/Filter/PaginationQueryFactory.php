<?php

namespace Applications\Repository\Filter;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the PaginationQuery
 *
 * @package Applications
 */
class PaginationQueryFactory implements FactoryInterface
{
    /**
     * Creates pagination Service 
     * 
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $auth  = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new PaginationQuery($auth);
        return $filter;
    }
}