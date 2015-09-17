<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SocialProfilesFactory.php */
namespace Auth\Controller\Plugin\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Auth\Controller\Plugin\SocialProfiles;

class SocialProfilesFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services   = $serviceLocator->getServiceLocator();
        $hybridAuth = $services->get('HybridAuth');
        $plugin     = new SocialProfiles($hybridAuth);
        
        return $plugin;
    }
}
