<?php

namespace Applications\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Collection;

class AttachmentsCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService (ServiceLocatorInterface $serviceLocator)
    {
        $collection = new Collection('attachments');
        $collection->setLabel('Attachments')
                   ->setCount(0)
                   ->setShouldCreateTemplate(true)
                   ->setAllowAdd(true)
                   ->setTargetElement($serviceLocator->get('Applications/AttachmentsFieldset'));
                   
        return $collection;
    }
    
    
}