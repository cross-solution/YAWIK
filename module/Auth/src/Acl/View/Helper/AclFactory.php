<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** AclFactory.php */ 
namespace Acl\View\Helper;

use Zend\ServiceManager\FactoryInterface;

class AclFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $acl      = $services->get('acl');
        $user     = $services->get('AuthenticationService')->getUser();
        
        $helper = new Acl($acl, $user);
        return $helper;
    }

    
}

