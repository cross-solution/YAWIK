<?php

namespace Applications\Filter;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class ParamsToPropertiesFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $jobMapper = $serviceLocator->getServiceLocator()->get('mappers')->get('job');
        $auth                   = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new ParamsToProperties($jobMapper, $auth);
        return $filter;
    }
}