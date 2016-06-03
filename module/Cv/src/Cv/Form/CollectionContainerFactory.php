<?php

namespace Cv\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CollectionContainerFactory implements FactoryInterface
{
    
    /**
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new \Core\Form\CollectionContainer($serviceLocator, $serviceLocator->get('CvFieldset'));
    }
}
