<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Paginator;

use Core\Repository\RepositoryService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Paginator\Paginator;

/**
 * Class PaginatorFactoryAbstract
 * @package Core\Paginator
 */
abstract class PaginatorFactoryAbstract implements FactoryInterface, MutableCreationOptionsInterface
{

    protected $options = [];

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }


    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed|Paginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var PaginatorService $serviceLocator */
        /* @var RepositoryService $repositories */
        $repositories   = $serviceLocator->getServiceLocator()->get('repositories');
        $repository     = $repositories->get($this->getRepository());
        $queryBuilder   = $repository->createQueryBuilder();
        $filter         = $serviceLocator->getServiceLocator()->get('filterManager')->get($this->getFilter());
        $adapter       = new \Core\Paginator\Adapter\DoctrineMongoLateCursor($queryBuilder, $filter, $this->options);
        $service        = new Paginator($adapter);

        $this->setCreationOptions([]);
        return $service;
    }

    /**
     * pagination service name
     *
     * @return string
     */
    abstract protected function getFilter();

    /**
     * repository name
     *
     * @return string
     */
    abstract protected function getRepository();
}