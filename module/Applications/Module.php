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


/**
 * Bootstrap class of the applications module
 * 
 */
class Module implements FormElementProviderInterface
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
    
    public function onBootstrap(MvcEvent $e)
    {
        
    }
    
    public function getFormElementConfig()
    {
    	return array(
    			'invokables' => array(
    					'phone' => 'Application\Form\Element\Phone'
    			),
    			'factories' => array(
    					'Contact' => 'Applications\Form',
    					'ContactFieldset' => 'Applications\Form'
    			),
 
    	);
    	
    }
    
}
