<?php

namespace Cv\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EducationCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = new \Core\Form\CollectionContainer('CvEducationForm', new \Cv\Entity\Education());
        $container->setLabel(/*@translate */ 'Education history');
        
        return $container;
    }
}
