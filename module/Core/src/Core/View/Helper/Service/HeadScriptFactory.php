<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\HeadScript;

/**
 * 
 */
class HeadScriptFactory implements FactoryInterface 
{

    /**
     * Creates an instance of \Zend\View\Helper\Headscript
     * 
     * - injects the MvcEvent instance
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @return HeadScript
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $helper \Zend\View\Helper\Headscript|\Callable */
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */
        /* @var $services \Zend\ServiceManager\ServiceLocatorInterface */
        $helper   = $serviceLocator->get('headscript'); //new HeadScript();
        $services = $serviceLocator->getServiceLocator();
        $config   = $services->get('Config');
        
        if (!isset($config['view_helper_config']['headscript'])) {
            return $helper;
        }
        
        $config     = $config['view_helper_config']['headscript'];

        /* @var $routeMatch \Zend\Mvc\Router\RouteMatch */
        $routeMatch = $services->get('Application')->getMvcEvent()->getRouteMatch();
        $routeName  = $routeMatch ? $routeMatch->getMatchedRouteName() : '';
        $basepath = $serviceLocator->get('basepath'); /* @var $basepath \Zend\View\Helper\BasePath */
        
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
    
}