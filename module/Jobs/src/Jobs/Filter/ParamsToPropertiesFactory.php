<?php

namespace Jobs\Filter;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class ParamsToPropertiesFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $auth                   = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new ParamsToProperties($auth);
        return $filter;
    }
}