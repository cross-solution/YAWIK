<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Factory\Service;

use Hybrid_Auth;
use Interop\Container\ContainerInterface;
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
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return Hybrid_Auth
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Making sure the SessionManager is initialized
        // before creating HybridAuth components
        $container->get('SessionManager')->start();

        $options = $container->get('Config');

        $hauthOptions = $options['hybridauth'];

        $router = $container->get('Router');

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

    /**
     * @param ServiceLocatorInterface $services
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, Hybrid_Auth::class);
    }
}
