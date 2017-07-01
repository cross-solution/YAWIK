<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Paginator\Paginator as ZendPaginator;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Mvc\Controller\PluginManager as ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * returns a paginator,
 * this class will propably not change much, since all the diversity is handled by it's adapter
 *
 * Class CreatePaginatorService
 *
 * @deprecated since 0.24. This plugin is clumsy and buggy. Use rewritten CreatePaginator instead.
 */
class CreatePaginatorService extends AbstractPlugin
{
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;
    
    /**
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param       $paginatorName
     * @param array $defaultParams
     * @param bool  $usePostParams
     *
     * @return mixed
     */
    public function __invoke($paginatorName, $defaultParams = array(), $usePostParams = false)
    {
        if (is_bool($defaultParams)) {
            $usePostParams = $defaultParams;
            $defaultParams = array();
        }

        if (!is_array($defaultParams) && !$defaultParams instanceof \Traversable) {
            throw new \InvalidArgumentException('$defaultParams must be an array or implement \Traversable');
        }

        $paginatorManager = $this->serviceManager->get('Core/PaginatorService');
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
    
    /**
     * @param ControllerManager $controllerManager
     * @return CreatePaginatorService
     */
    public static function factory(ContainerInterface $container)
    {
        return new static($container);
    }
}