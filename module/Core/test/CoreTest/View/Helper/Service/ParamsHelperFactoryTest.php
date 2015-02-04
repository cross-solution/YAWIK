<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\View\Helper\Service;

use Core\View\Helper\Service\ParamsHelperFactory as Factory;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperPluginManager;

class ParamsHelperFactoryTest extends \PHPUnit_Framework_TestCase
{
    
    public function testFactoryReturnsInstanceOfParamsHelper()
    {
        $factory = new Factory();
        $sm = new ServiceManager();
        
        $event = new MvcEvent();
        
        $application = $this->getMockBuilder('\Zend\Mvc\Application')
            ->disableOriginalConstructor()
            ->setMethods(array('getMvcEvent'))
            ->getMock();
        
        $application->expects($this->once())
            ->method('getMvcEvent')
            ->will($this->returnValue($event));
        
        $sm->setService('Application', $application);
        
        $hm = new HelperPluginManager();
        $hm->setServiceLocator($sm);
        
        $helper = $factory->createService($hm);
        $this->assertInstanceOf('\Core\View\Helper\Params', $helper);
    }
    
    
}