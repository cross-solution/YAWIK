<?php

namespace Applications\Repository\EntityBuilder;

use Core\Repository\EntityBuilder\FileBuilderFactory;

class AttachmentsBuilderFactory extends FileBuilderFactory
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $builder = parent::createService($serviceLocator);
        $mapper  = $serviceLocator->getServiceLocator()->get('mappers')->get('Applications/Files');
        $builder->setMapper($mapper);
        return $builder;        
    }
    
    
    
}