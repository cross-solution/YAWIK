<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\Model;

use Interop\Container\ContainerInterface;
use Jobs\Model\ApiJobDehydrator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for ApiJobDehydrator
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class ApiJobDehydratorFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewManager = $container->get('ViewHelperManager');
        $urlHelper = $viewManager->get('url');
        $apiJobDehydrator = new ApiJobDehydrator();
        $apiJobDehydrator->setUrl($urlHelper);
        return $apiJobDehydrator;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ApiJobDehydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
;
        return $this($serviceLocator, ApiJobDehydrator::class);
    }
}
