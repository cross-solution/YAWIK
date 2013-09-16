<?php

namespace Applications\Filter;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class ParamsToPropertiesFactory implements FactoryInterface
{
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repository = $serviceLocator->getServiceLocator()->get('repositories')->get('job');
        $auth                   = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new ParamsToProperties($repository, $auth);
        return $filter;
    }
}