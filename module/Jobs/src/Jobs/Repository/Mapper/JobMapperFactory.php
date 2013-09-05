<?php

namespace Jobs\Repository\Mapper;

use Zend\ServiceManager\FactoryInterface;

class JobMapperFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $db = $serviceLocator->getServiceLocator()->get('MongoDb');
        $mapper = new JobMapper($db->jobs);
         
        return $mapper;
    }

    
}