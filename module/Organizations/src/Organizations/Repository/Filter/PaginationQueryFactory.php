<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationQueryFactory.php */
namespace Organizations\Repository\Filter;

use Interop\Container\ContainerInterface;
use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the PaginationQuery
 *
 * @package Organizations
 * @author Mathias Weitz <weitz@cross-solution.de>
 */

class PaginationQueryFactory implements FactoryInterface
{
    /**
     * Create a PaginationQuery
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return PaginationQuery
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth = $container->get('AuthenticationService');
        $filter = new PaginationQuery($auth);
        return $filter;
    }
    /**
     * Creates pagination Service
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Organizations\Repository\Filter\PaginationQuery|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */
        return $this($serviceLocator->getServiceLocator(), PaginationQuery::class);
    }
}
