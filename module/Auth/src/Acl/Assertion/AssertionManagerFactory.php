<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Acl\Assertion;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Config;

/**
 * Factory for creating the AssertionManager.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AssertionManagerFactory implements FactoryInterface
{
    /**
     * Creates an instance of \Auth\View\Helper\Auth
     *
     * - Injects the AuthenticationService
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AssertionManager
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configArray = $serviceLocator->get('Config');
        $configArray = isset($configArray['acl']['assertions'])
                     ? $configArray['acl']['assertions']
                     : array();
        $config      = new Config($configArray);
        $manager     = new AssertionManager($serviceLocator, $config);
        
        $manager->setShareByDefault(false);
        return $manager;
    }
}
