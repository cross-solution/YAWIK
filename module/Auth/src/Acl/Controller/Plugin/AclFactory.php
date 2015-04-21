<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AclFactory.php */ 
namespace Acl\Controller\Plugin;

use Zend\ServiceManager\FactoryInterface;

/**
 * Class AclFactory
 * @package Acl\Controller\Plugin
 */
class AclFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $acl      = $services->get('acl');
        $auth     = $services->get('AuthenticationService');

        $plugin = new Acl($acl, $auth->getUser());
        return $plugin;
    }

    
}

