<?php
/**
 * YAWIK
 * Auth Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv;

use Zend\Mvc\MvcEvent;
use Auth\View\InjectLoginInfoListener;
use Auth\Listener\TokenListener;

/**
 * Bootstrap class of the Core module
 *
 */
class Module
{

    
    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function onBootstrap(MvcEvent $e)
    {
    }
}
