<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** PaginationQueryFactory.php */
namespace Applications\Repository\Filter;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use \Laminas\ServiceManager\Factory\FactoryInterface;
use \Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the PaginationQuery
 *
 * @author  Carsten Bleek <bleek@cross-solution.de>
 * @author  Anthonius Munthi <me@itstoni.com>
 * @package Applications
 */
class PaginationQueryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth  = $container->get('AuthenticationService');
        $filter = new PaginationQuery($auth);
        return $filter;
    }

    /**
     * Creates pagination Service
     *
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     *
     * @param ContainerInterface $container
     * @return PaginationQuery|mixed
     * @internal param ServiceLocatorInterface $serviceLocator
     */
    public function createService(ContainerInterface $container)
    {
        return $this($container, PaginationQuery::class);
    }
}
