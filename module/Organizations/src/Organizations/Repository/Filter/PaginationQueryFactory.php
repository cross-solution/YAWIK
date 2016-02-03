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
     * Creates pagination Service
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Organizations\Repository\Filter\PaginationQuery|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $auth  = $serviceLocator->getServiceLocator()->get('AuthenticationService');
        $filter = new PaginationQuery($auth);
        return $filter;
    }
}
