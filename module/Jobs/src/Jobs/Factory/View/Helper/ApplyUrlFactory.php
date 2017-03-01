<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Factory\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Jobs\View\Helper\ApplyUrl;

/**
 * Factory for ApplyUrl view helper
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ApplyUrlFactory implements FactoryInterface
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
        $helper    = new ApplyUrl();
        $url       = $container->get('url');
        $translate = $container->get('translate');
        $params    = $container->get('params');
        $serverUrl = $container->get('serverUrl');
        $helper->setUrlHelper($url)
               ->setTranslateHelper($translate)
               ->setParamsHelper($params)
               ->setServerUrlHelper($serverUrl);
        return $helper;
    }


    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ApplyUrl::class);
    }
}
