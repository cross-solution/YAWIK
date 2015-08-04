<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Paginator\Paginator as ZendPaginator;
use DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * returns a paginator,
 * this class will propably not change much, since all the diversity is handled by it's adapter
 *
 * Class CreatePaginatorService
 * @package Core\Controller\Plugin
 */
class CreatePaginatorService extends AbstractPlugin {

    public function __invoke($paginatorName, $defaultParams = array(), $usePostParams = false)
    {
        if (is_bool($defaultParams)) {
            $usePostParams = $defaultParams;
            $defaultParams = array();
        }

        if (!is_array($defaultParams) && !$defaultParams instanceOf \Traversable) {
            throw new \InvalidArgumentException('$defaultParams must be an array or implement \Traversable');
        }

        $services   = $this->getController()->getServiceLocator();
        $paginatorManager = $services->get('Core/PaginatorService');
        $paginator = $paginatorManager->get($paginatorName);
        if (!isset($paginator) || !$paginator instanceof ZendPaginator) {
            throw new \RuntimeException('Could not create paginator ' . $paginatorName);
        }
        $adapter = $paginator->getAdapter();
        if (!isset($adapter) || !$adapter instanceof AdapterInterface) {
            throw new \RuntimeException('Paginator ' . $paginatorName . ' has no Adapter');
        }

        $params     = $usePostParams
            ? $this->getController()->getRequest()->getPost()
            : $this->getController()->getRequest()->getQuery();
        $params     = clone $params; // prevent param changes to original object.

        foreach ($defaultParams as $name => $value) {
            $params->set($name, $params->get($name, $value));
        }

        $this->filterSortParam($params);
        //$paginator = $this->createPaginator($repository, $params);
        $adapter->setParams($params);
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
} 