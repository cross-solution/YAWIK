<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Repository\Filter;

use Auth\AuthenticationService;
use Jobs\Repository\Filter\PaginationAdminQuery;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaginationAdminQueryFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return PaginationAdminQuery|mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $services \Zend\ServiceManager\ServiceManager */
        $services = $serviceLocator->getServiceLocator();
        /* @var AuthenticationService $auth */
        $auth = $services->get('AuthenticationService');
        $acl = $services->get('Acl');

        $filter = new PaginationAdminQuery($auth, $acl);
        return $filter;
    }
}
