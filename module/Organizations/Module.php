<?php
/**
 * YAWIK
 * Organizations Module Bootstrap
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations;

use Core\ModuleManager\ModuleConfigLoader;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;

/**
 * Bootstrap class of the organizations module
 */
class Module implements \Zend\ModuleManager\Feature\BootstrapListenerInterface
{
    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
         return ModuleConfigLoader::load(__DIR__ . '/config');
    }
    
    /**
     * Loads module specific autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'OrganizationsTest' => __DIR__ . '/test/' . 'OrganizationsTest'
                ),
            ),
        );
    }

    public function onBootstrap(EventInterface $e)
    {
        /* @var $e MvcEvent */
        $eventManager = $e->getApplication()->getEventManager();
        $services     = $e->getApplication()->getServiceManager();
        $sharedManager = $eventManager->getSharedManager();

        $createJobListener = new \Organizations\Acl\Listener\CheckJobCreatePermissionListener();
        $createJobListener->attachShared($sharedManager);



    }
}
