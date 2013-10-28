<?php

namespace Cv\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Collection;

class EducationCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $collection = new Collection('educations');
        $collection->setLabel('Education history')
                   ->setCount(0)
                   ->setShouldCreateTemplate(true)
                   ->setAllowAdd(true)
                   ->setTargetElement($serviceLocator->get('EducationFieldset'));
        return $collection;
    }
    
    
}