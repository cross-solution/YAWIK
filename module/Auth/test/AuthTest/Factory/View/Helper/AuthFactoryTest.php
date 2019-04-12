<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */


namespace AuthTest\Factory\View\Helper;

use PHPUnit\Framework\TestCase;

use Auth\Factory\View\Helper\AuthFactory;
use Auth\View\Helper\Auth;
use Zend\ServiceManager\ServiceManager;
use Zend\View\HelperPluginManager;

class AuthFactoryTest extends TestCase
{
    public function testFactoryReturnsProperConfiguredInstanceOfAuthViewHelperAuth()
    {
        $f = new AuthFactory();
        $sm = new ServiceManager();
        $auth = $this->getMockBuilder('\Zend\Authentication\AuthenticationService')->getMock();
        $sm->setService('AuthenticationService', $auth);
        
        $hm = new HelperPluginManager($sm);
        
        $helper = $f->__invoke($sm, Auth::class);
        
        $this->assertInstanceOf('\Auth\View\Helper\Auth', $helper);
        $this->assertSame($auth, $helper->getService());
    }
}
