<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
    /**
     * Creates an instance of \Hybrid_Auth
     * 
     * - reads config from the application configuration array
     *   under the key 'hybridauth' and passes it as the key
     *   'providers' to the \Hybrid_Auth instance.
     *   
     * - assembles the route "auth/hauth" and pass it as 
     *   'base_url' to the \Hybrid_Auth instance.
     *   
     * @param ServiceLocatorInterface $services
     * @return \Hybrid_Auth
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
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
                'name' => 'auth-hauth',
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
