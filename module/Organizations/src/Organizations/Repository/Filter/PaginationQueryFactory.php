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
use \Zend\ServiceManager\Factory\FactoryInterface;

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
}
