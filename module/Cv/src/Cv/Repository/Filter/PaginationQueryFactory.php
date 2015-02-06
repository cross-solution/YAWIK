<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationQueryFactory.php */ 
namespace Cv\Repository\Filter;

use Zend\ServiceManager\FactoryInterface;

class PaginationQueryFactory implements FactoryInterface
{
	public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $auth     = $services->get('AuthenticationService');
        $user     = $auth->hasIdentity() ? $auth->getUser() : null;
        $filter   = new PaginationQuery($user);
        
        return $filter;
        
    }

    
}

