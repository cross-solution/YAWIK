<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationQueryFactory.php */
namespace Cv\Repository\Filter;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PaginationQueryFactory
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @package Cv\Repository\Filter
 */
class PaginationQueryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth     = $container->get('AuthenticationService');
        $user     = $auth->hasIdentity() ? $auth->getUser() : null;
        $filter   = new PaginationQuery($user);
        
        return $filter;
    }
}
