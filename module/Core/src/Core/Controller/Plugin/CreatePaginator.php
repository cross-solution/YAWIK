<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Mapper\Query\Query;
use Zend\Paginator\Paginator;
use DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator;

class CreatePaginator extends AbstractPlugin
{
    
    
    public function __invoke($filterName)
    {
        $services     = $this->getController()->getServiceLocator();
        $queryFilter  = $services->get('filtermanager')->get($filterName);
        $params       = $this->getController()->getRequest()->getQuery();
        $queryBuilder = $services->get('repositories')->createQueryBuilder();
        $query        = $queryFilter->filter($params, $queryBuilder);
        $cursor       = $query->execute();
        
        $adapterClass = method_exists($queryFilter, 'getPaginatorAdapterClass')
                        ? $queryFilter->getPaginatorAdapterClass()
                        : '\DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator';
        $adapter      = new $adapterClass($cursor);
        $paginatorClass = method_exists($queryFilter, 'getPaginatorClass')
                          ? $queryFilter->getPaginatorClass()
                          : '\Zend\Paginator\Paginator';
        
        $paginator    = new $paginatorClass($adapter);
        
        $page  = $params->get('page', 1);
        $count = $params->get('count', 10);
        
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($count);
        
        return $paginator;
    }
    
	
}
