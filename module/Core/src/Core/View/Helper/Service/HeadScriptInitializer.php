<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** HeadScriptInitializer.php */ 
namespace Core\View\Helper\Service;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\HeadScript;

class HeadScriptInitializer implements InitializerInterface
{
    
    protected $isInitialized = false;
    
    public function initialize ($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceOf HeadScript && !$this->isInitialized) {
            $this->initializeHeadscriptHelper($instance, $serviceLocator);
        }
    }
    
    public function initializeHeadscriptHelper($helper, $helperManager)
    {
        $this->isInitialized = true;
        $services = $helperManager->getServiceLocator();
        $config   = $services->get('Config');
        if (!isset($config['view_inject_headscript'])) {
            return;
        }
        $config     = $config['view_inject_headscript'];
        $routeMatch = $services->get('Application')->getMvcEvent()->getRouteMatch();
        $routeName  = $routeMatch ? $routeMatch->getMatchedRouteName() : ''; 
        
        $basepath = $helperManager->get('basepath');
        foreach ($config as $routeStart => $scripts) {
            if (is_int($routeStart) || 0 === strpos($routeName, $routeStart)) {
                if (!is_array($scripts)) {
                    $scripts = array($scripts);
                }
                foreach ($scripts as $script) {
                    $helper->appendFile($basepath($script));
                }
            }
        }
    }

    
}

