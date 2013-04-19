<?php

namespace Core\Mapper\Query\Service;

use Zend\ServiceManager\FactoryInterface;
use Core\Mapper\Query\Query;

class QueryFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $query = new Query();
        $queryServiceManager = $serviceLocator->get('query_service_manager');
        $query->setServiceManager($queryServiceManager);
        return $query;
    }

    
}
