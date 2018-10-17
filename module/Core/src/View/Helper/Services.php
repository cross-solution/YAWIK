<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helper */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\HelperPluginManager;

/**
 * Provides access to the service manager.
 *
 * Simplifies descending into other service locators (i.e. plugin managers) by
 * allowing the use of dot-notation.
 *
 * <code>
 *      // Get the service manager.
 *      $sm = $this->services();
 *
 *      // Get a service
 *      $service = $this->services('myService');
 *
 *      // Get a plugin from a plugin manager
 *      $plugin = $this->services('pluginManager.pluginName');
 *
 *      // to get a service that contains dots in its name ("my.service"):
 *      // 1. Either don't type them (as names are normalized within the SM anyway.)
 *      $this->services('myservice');
 *
 *      // 2. Get the service manager and use its get() method
 *      $this->services()->get('my.service');
 *
 * </code>
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Services extends AbstractHelper
{
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;
    
    /**
     * @param ServiceLocatorInterface $serviceManager
     */
    public function __construct(ServiceLocatorInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
    /**
     * Gets a service from the service manager.
     *
     * if __$serviceName__ is _NULL_, the service manager itself is returned.
     *
     * You may use dot-notation to descent into services which are itself a
     * service locator (i.e. plugin managers).
     *
     * @param string|null $serviceName
     * @return \Zend\ServiceManager\ServiceLocatorInterface|mixed
     */
    public function __invoke($serviceName = null)
    {
        if (null === $serviceName) {
            return $this->serviceManager;
        }
        
        if (strpos($serviceName, '.') !== false) {
            $parts = explode('.', $serviceName);
            $service = $this->serviceManager;
            foreach ($parts as $name) {
                $service = $service->get($name);
            }
            return $service;
        }
        
        return $this->serviceManager->get($serviceName);
    }
    
    /**
     * @param HelperPluginManager $helperPluginManager
     * @return Services
     */
    public static function factory($sm)
    {
        return new static($sm);
    }
}
