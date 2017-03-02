<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   MIT
 */

namespace Core\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\View\Helper\SocialButtons;

class SocialButtonsFactory implements FactoryInterface {

    /**
     * Create an SocialButtons view helper
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return SocialButtons
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $serviceLocator \Zend\View\HelperPluginManager */
        $options = $container->get('Auth/Options');
        $config = $container->get('Config');
        $helper = new SocialButtons($options,$config);
        return $helper;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return SocialButtons
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), SocialButtons::class);
    }
}

