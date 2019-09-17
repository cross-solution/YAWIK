<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace CoreTest\View\Helper\Service;

use PHPUnit\Framework\TestCase;

use Core\View\Helper\Service\ParamsHelperFactory as Factory;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperPluginManager;

class ParamsHelperFactoryTest extends TestCase
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
        
        //$hm = new HelperPluginManager($sm);
        //$hm->setServiceLocator($sm);
        
        $helper = $factory->createService($sm);
        $this->assertInstanceOf('\Core\View\Helper\Params', $helper);
    }
}
