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

use Core\Paginator\Adapter\DoctrineMongoLateAdapter;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
//use Laminas\ServiceManager\MutableCreationOptionsInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Paginator\Paginator;

/**
 * Class PaginatorFactoryAbstract
 *
 *
 * @since 0.30 - ZF3 compatibility
 */
abstract class PaginatorFactoryAbstract implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var PaginatorService $paginatorService */
        /* @var RepositoryService $repositories */
        $repositories   = $container->get('repositories');
        $repository     = $repositories->get($this->getRepository());
        $queryBuilder   = $repository->createQueryBuilder();
        $filter         = $container->get('FilterManager')->get($this->getFilter());
        $adapter        = new DoctrineMongoLateAdapter($queryBuilder, $filter, $options);

        return new Paginator($adapter);
    }
    
    /**
     * @param ContainerInterface $container
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
