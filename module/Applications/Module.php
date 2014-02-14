<?php
/**
 * Cross Applicant Management
 * Applications Module Bootstrap
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications;

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\View\ViewEvent;
use Zend\View\Renderer\PhpRenderer;


/**
 * Bootstrap class of the applications module
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
        return include __DIR__ . '/config/module.config.php';
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
    
    public function onBootstrap(MvcEvent $mvcEvent)
    {
        // Ignore the form annotations in setting entities
        AnnotationReader::addGlobalIgnoredName('formLabel');
    }
    
}
