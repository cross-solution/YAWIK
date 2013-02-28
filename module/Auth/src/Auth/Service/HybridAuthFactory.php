<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Service;

use Hybrid_Auth;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating the Hybrid_Auth instance.
 */
class HybridAuthFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        // Making sure the SessionManager is initialized
        // before creating HybridAuth components
        $sessionManager = $services->get('SessionManager')->start();

        
        $options = $services->get('Config');
        
        $hauthOptions = $options['hybridauth'];

        $router = $services->get('Router');
        
        $baseUrl = $router->assemble(
            array(),
            array(
                'name' => 'auth/hauth',
                'force_canonical' => true,
            )
        );
        
        $hybridAuth = new Hybrid_Auth(
            array(
                'base_url' => $baseUrl,
                'providers' => $hauthOptions
                
            )
        );

        return $hybridAuth;
    }
}
