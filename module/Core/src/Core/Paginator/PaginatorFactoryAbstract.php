<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Paginator;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Parameters;
use Zend\Paginator\Paginator;

/**
 * Class PaginatorFactoryAbstract
 * @package Core\Paginator
 */
abstract class PaginatorFactoryAbstract implements FactoryInterface {

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed|Paginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $repositories   = $serviceLocator->getServiceLocator()->get('repositories');
        $repository     = $repositories->get($this->getRepository());
        $queryBuilder   = $repository->createQueryBuilder();
        $filter         = $serviceLocator->getServiceLocator()->get('filterManager')->get($this->getFilter());

        $adapter       = new \Core\Paginator\Adapter\DoctrineMongoLateCursor($queryBuilder, $filter);

        $service        = new Paginator($adapter);
        return $service;
    }

    /**
     * @return Zend\Filter\FilterInterface
     */
    abstract protected function getFilter();

    /**
     * @return Zend\Filter\FilterInterface\Repository
     */
    abstract protected function getRepository();

} 