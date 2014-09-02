<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** AclFactory.php */ 
namespace Acl\View\Helper;

use Zend\ServiceManager\FactoryInterface;

/**
 * Class AclFactory
 * @package Acl\View\Helper
 */
class AclFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $plugins  = $services->get('controllerpluginmanager');
        $acl      = $plugins->get('acl');
        
        $helper = new Acl($acl);
        return $helper;
    }

    
}

