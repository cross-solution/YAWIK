<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Paginator\Paginator;
use DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator;

/**
 * @deprecated
 * this plugin should get replaced by Core\Paginator\PaginatorService
 *
 * Class CreatePaginator
 * @package Core\Controller\Plugin
 */
class CreatePaginator extends AbstractPlugin
{
    
    public function __invoke($repositoryName, $defaultParams = array(), $usePostParams = false)
    {
        if (is_bool($defaultParams)) {
            $usePostParams = $defaultParams;
            $defaultParams = array();
        }
        
        if (!is_array($defaultParams) && !$defaultParams instanceOf \Traversable) {
            throw new \InvalidArgumentException('$defaultParams must be an array or implement \Traversable');
        }
        
        
        $services   = $this->getController()->getServiceLocator();
        $repository = $services->get('repositories')->get($repositoryName);
        $params     = $usePostParams 
                    ? $this->getController()->getRequest()->getPost()
                    : $this->getController()->getRequest()->getQuery();
        $params     = clone $params; // prevent param changes to original object.
        
        foreach ($defaultParams as $name => $value) {
            $params->set($name, $params->get($name, $value));
        }
        
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
            $adapter = new \Core\Paginator\Adapter\DoctrineMongoCursor($cursor);
            
        } else {
            throw new \RuntimeException('Could not create paginator for repository ' . get_class($repository));
        }
        $paginator = new \Zend\Paginator\Paginator($adapter);
        return $paginator;
    }
    
    
}
