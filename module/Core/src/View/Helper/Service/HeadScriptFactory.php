<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\HeadScript;

/**
 *
 */
class HeadScriptFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $helper \Laminas\View\Helper\Headscript|\Callable */
        /* @var $container \Laminas\ServiceManager\AbstractPluginManager */
        /* @var $services \Laminas\ServiceManager\ServiceLocatorInterface */
        $viewHelperManager = $container->get('ViewHelperManager');
        $helper   = $viewHelperManager->get('headscript'); //new HeadScript();
        $services = $container;
        $config   = $services->get('Config');
        
        if (!isset($config['view_helper_config']['headscript'])) {
            return $helper;
        }
        
        $config     = $config['view_helper_config']['headscript'];
        
        /* @var $routeMatch \Laminas\Router\RouteMatch */
        $routeMatch = $services->get('Application')->getMvcEvent()->getRouteMatch();
        $routeName  = $routeMatch ? $routeMatch->getMatchedRouteName() : '';
        $basepath = $viewHelperManager->get('basepath'); /* @var $basepath \Laminas\View\Helper\BasePath */
        
        foreach ($config as $routeStart => $specs) {
            if (!is_int($routeStart)) {
                if (0 !== strpos($routeName, $routeStart)) {
                    continue;
                }
            } else {
                $specs = array($specs);
            }
            
            if (is_string($specs)) {
                $helper->appendScript('// if you are missing the script ' . $specs . ' look up your config and enclose it in an array');
                continue;
            }
            
            foreach ($specs as $spec) {
                if (is_string($spec)) {
                    $helper->appendFile($basepath($spec));
                    continue;
                }
                
                if ($helper::SCRIPT != $spec[0]) {
                    $spec[1] = $basepath($spec[1]);
                }
                
                call_user_func_array($helper, $spec);
            }
        }
        
        return $helper;
    }
    
    
    /**
     * Creates an instance of \Laminas\View\Helper\Headscript
     *
     * - injects the MvcEvent instance
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return HeadScript
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, HeadScript::class);
    }
}
