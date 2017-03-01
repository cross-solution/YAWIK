<?php

namespace Jobs\Factory\Repository\Filter;

use Auth\AuthenticationService;
use Interop\Container\ContainerInterface;
use Jobs\Repository\Filter\PaginationQuery;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PaginationQueryFactory
 * @package Jobs\Repository\Filter
 */
class PaginationQueryFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $services \Zend\ServiceManager\ServiceManager */
        $services = $container->getServiceLocator();
        /* @var AuthenticationService $auth */
        $auth = $services->get('AuthenticationService');
        $acl = $services->get('Acl');

        $filter = new PaginationQuery($auth, $acl);
        return $filter;
    }

    /**
     * @param ServiceLocatorInterface $services
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, PaginationQuery::class);
    }
}
