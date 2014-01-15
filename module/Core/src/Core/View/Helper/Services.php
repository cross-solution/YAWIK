<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * provides access to the viewManager
 * @todo Write factory, configuration must be possible
 * @author mathias
 *
 */


class Services extends AbstractHelper implements ServiceLocatorAwareInterface
{
    
    protected $services;
    
    public function getServiceLocator()
    {
        return $this->services;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator->getServiceLocator();
        return $this;
    }
    
    public function __invoke($serviceName=null)
    {
        if (null === $serviceName) {
            return $this->getServiceLocator();
        }
        
        if (strpos($serviceName, '.') !== false) {
            $parts = explode('.', $serviceName);
            $service = $this->getServiceLocator();
            foreach ($parts as $name) {
                $service = $service->get($name); 
            }
            return $service;
        }
        
        return $this->getServiceLocator()->get($serviceName);
    }

    
    
    

    
}