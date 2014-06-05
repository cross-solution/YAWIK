<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */


namespace AuthTest\Service;

use Auth\Service\AuthViewHelperFactory as Factory;
use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;

class AuthViewHelperFactoryTest extends \PHPUnit_Framework_TestCase
{
    
    public function testFactoryReturnsProperConfiguredInstanceOfAuthViewHelperAuth()
    {
        $f = new Factory();
        $sm = new ServiceManager();
        $auth = $this->getMock('\Zend\Authentication\AuthenticationService');
        $sm->setService('AuthenticationService', $auth);
        
        $hm = new HelperPluginManager();
        $hm->setServicelocator($sm);
        
        
        $helper = $f->createService($hm);
        
        $this->assertInstanceOf('\Auth\View\Helper\Auth', $helper);
        $this->assertSame($auth, $helper->getService());
    }
}