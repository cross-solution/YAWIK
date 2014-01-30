<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Mapper\Query\Query;
use Zend\Paginator\Paginator;
use DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator;

class CreatePaginator extends AbstractPlugin
{
    
    public function __invoke($repositoryName, $usePostParams = false)
    {
        $services   = $this->getController()->getServiceLocator();
        $repository = $services->get('repositories')->get($repositoryName);
        $params     = $usePostParams 
                    ? $this->getController()->getRequest()->getPost()
                    : $this->getController()->getRequest()->getQuery();
        $this->filterSortParam($params);
        $paginator = $this->createPaginator($repository, $params);
        $paginator->setCurrentPageNumber($params->get('page', 1))
                  ->setItemCountPerPage($params->get('count', 10));
        
        return $paginator;
        
    }
    
    protected function filterSortParam($params)
    {
        $sort = $params->get('sort');
        if (null === $sort) {
            return;
        }
        
        if (0 === strpos($sort, '-')) {
            $dir = '-1';
            $sort = substr($sort, 1);
        } else {
            $dir = '1';
        }
        
        $params->set('sortField', $sort);
        $params->set('sortDir', $dir);
    }
    
    /**
     * returns a Paginator.
     * 
     * @param Repository $repository
     * @param Params $params
     * @throws \RuntimeException
     * @return \Zend\Paginator\Paginator
     */
    protected function createPaginator($repository, $params)
    {
        if (method_exists($repository, 'getPaginator')) {
            return $repository->getPaginator($params);
        }
        
        if (method_exists($repository, 'getPaginatorAdapter')) {
            $adapter = $repository->getPaginatorAdapter($params);
            
        } else if (method_exists($repository, 'getPaginatorCursor')) {
            $cursor = $repository->getPaginatorCursor($params);
            $adapter = new \DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator($cursor);
            
        } else {
            throw new \RuntimeException('Could not create paginator for repository ' . get_class($repository));
        }
        $paginator = new \Zend\Paginator\Paginator($adapter);
        return $paginator;
    }
    
    public function __invokes($filterName)
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
