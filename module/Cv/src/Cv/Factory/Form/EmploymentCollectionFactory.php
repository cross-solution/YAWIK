<?php

namespace Cv\Factory\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmploymentCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = new \Core\Form\CollectionContainer('CvEmploymentForm', new \Cv\Entity\Employment());
        $container->setLabel(/*@translate */ 'Employment history');
        
		return $container;
    }
}
