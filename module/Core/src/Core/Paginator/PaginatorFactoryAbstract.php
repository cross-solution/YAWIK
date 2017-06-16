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
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
//use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Paginator\Paginator;

/**
 * Class PaginatorFactoryAbstract
 * @package Core\Paginator
 */
abstract class PaginatorFactoryAbstract implements FactoryInterface
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

    public function __invoke( ContainerInterface $paginatorService, $requestedName, array $options = null )
    {
	    /* @var PaginatorService $paginatorService */
	    /* @var RepositoryService $repositories */
	    $container      = $paginatorService->getContainer();
	    $repositories   = $container->get('repositories');
	    $repository     = $repositories->get($this->getRepository());
	    $queryBuilder   = $repository->createQueryBuilder();
	    $filter         = $container->get('FilterManager')->get($this->getFilter());
	    $adapter        = new \Core\Paginator\Adapter\DoctrineMongoLateCursor($queryBuilder, $filter, $this->options);
	    $service        = new Paginator($adapter);
	
	    $this->setCreationOptions([]);
	    return $service;
    }
	
	/**
     * @param ContainerInterface $serviceLocator
     * @return mixed|Paginator
     */
    public function createService(ContainerInterface $container)
    {
        return $this($container, get_class($this));
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