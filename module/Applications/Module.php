<?php
/**
 * YAWIK
 * Applications Module Bootstrap
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications;

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\View\ViewEvent;
use Zend\View\Renderer\PhpRenderer;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Core\ModuleManager\ModuleConfigLoader;


/**
 * Bootstrap class of the applications module
 */
class Module implements ConsoleUsageProviderInterface
{
    /**
     * Displays console options
     * 
     * @param Console $console
     */

    public function getConsoleUsage(Console $console)
    {
        return array(
            'Manipulation of applications database',
            'applications generatekeywords [--filter=]' => '(Re-)Generates keywords for all applications.',
            array('--filter=JSON', "available keys:\n"
                                  ."- 'before:ISODate' -> only applications before the given date\n"
                                  ."- 'after':ISODate' -> only applications after the given date\n"
                                  ."- 'limit':INT -> Limit result."),
            'applications calculate-rating [--filter=]' => '(Re-)Calculates average rating for all applications.',
            array('--filter=JSON', "available keys:\n"
                                  ."- 'before:ISODate' -> only applications before the given date\n"
                                  ."- 'after':ISODate' -> only applications after the given date\n"
                                  ."- 'limit':INT -> Limit result."),
        );
    }
    
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
                ),
            ),
        );
    }
    
    /**
     * Bootraps the application module
     * 
     * @param MvcEvent $mvcEvent
     */
    public function onBootstrap(MvcEvent $mvcEvent)
    {
        // Ignore the form annotations in setting entities
        AnnotationReader::addGlobalIgnoredName('formLabel');
    }
    
}
