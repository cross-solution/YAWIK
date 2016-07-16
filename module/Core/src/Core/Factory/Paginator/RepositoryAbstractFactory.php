<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Factory\Paginator;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract factory to create paginators from an entity repository.
 *
 * The main purpose of this class is to be able to quickly create paginators over an entity set,
 * without having to create factories and paginator classes.
 *
 * To work out of the box, there are a few assumptions:
 *
 * * The name of the paginator service must be a name also recognized by the repository service manager.
 *   (e.g. Namespace/EntityName). It can be prefixed with 'Repository/'. (e.g. Repository/Namespace/EntityName)
 *
 * * If you want to filter the query, there must be a filter in the filter manager with the name of the
 *   requested paginator prepended with 'PaginationQuery/' (e.g. PaginationQuery/Namespace/EntityName)
 *
 * * The options given to this factory through the service manager are passed AS IS to the filters' filter
 *   method. The second parameter to the filter is the QueryBuilder object for the specified entity (repository)
 *
 * There is currently one minor issue:
 *
 * * If you want to use more than one paginator for the same repository (with different options), you have to
 *   set the particular name in the 'shared' section of the paginator manager config in your module config file
 *   and assign it the value <i>false</i>.
 *   Example:
 *
 *   <pre>
 *      'paginator_manager' => [
 *          'shared' => [
 *              'Repository/Namespace/EntityName' => false,
 *          ],
 *      ],
 *   </pre>
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.24
 */
class RepositoryAbstractFactory implements AbstractFactoryInterface, MutableCreationOptionsInterface
{
    /**
     * Creation options.
     *
     * @var array
     */
    protected $options = [];

    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }


    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $class = $this->getEntityClassName($requestedName);

        return class_exists($class, true);
    }

    /**
     * Create a paginator instance with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param String                  $name
     * @param String                  $requestedName
     *
     * @return \Zend\Paginator\Paginator
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /* @var $serviceLocator \Core\Paginator\PaginatorService
         * @var $repositories \Core\Repository\RepositoryService
         * @var $filter       \Zend\Filter\FilterInterface
         */
        $services     = $serviceLocator->getServiceLocator();
        $repositories = $services->get('repositories');
        $queryBuilder = $repositories->createQueryBuilder();

        $queryBuilder->find($this->getEntityClassName($requestedName));

        $filterManager = $services->get('FilterManager');
        $filterName    = 'PaginationQuery/' . $requestedName;

        if ($filterManager->has($filterName)) {
            $filter       = $filterManager->get('PaginationQuery/' . $requestedName);
            $queryBuilder = $filter->filter($this->options, $queryBuilder);
        }

        $cursor    = $queryBuilder->getQuery()->execute();
        $adapter   = new \Core\Paginator\Adapter\DoctrineMongoCursor($cursor);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        // Clear creation options, because AbstractPluginManager does not call
        // setCreationOptions() when no options are passed.
        $this->setCreationOptions([]);


        return $paginator;
    }

    /**
     * Gets an entity class name from the requested service name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getEntityClassName($name)
    {
        $repositoryName = str_replace('Repository/', '', $name);
        $nameParts      = explode('/', $repositoryName);

        $namespace = $nameParts[0];
        $entity    = isset($nameParts[1]) ? $nameParts[1] : substr($namespace, 0, -1);
        $class     = "\\$namespace\\Entity\\$entity";

        return $class;
    }
}
