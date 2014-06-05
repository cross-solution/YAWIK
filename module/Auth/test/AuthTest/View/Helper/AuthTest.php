<?php
/**
 * YAWIK - Unit Tests
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */


namespace AuthTest\View\Helper;

use Auth\View\Helper\Auth;

class AuthTest extends \PHPUnit_Framework_TestCase
{

    private function getAuthMock()
    {
        $auth = $this->getMockBuilder('\Zend\Authentication\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();
        return $auth;
    }
    
    private function getPropertyTestAuthMock()
    {
        $auth = $this->getAuthMock();
        $auth->expects($this->once())
        ->method('hasIdentity')
        ->will($this->returnValue(true));
        
        $user = new \Auth\Model\User();
        $user->setData(array(
            'id' => 'testUser',
            'firstName' => 'test',
            'lastName' => 'user',
        ));
        $auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue($user));
        return $auth;
    }
    
    public function testSettingAndGettingAuthService()
    {
        $auth = $this->getAuthMock();
        $helper = new Auth();
        $this->assertSame($helper, $helper->setService($auth));
        $this->assertSame($auth, $helper->getService());
    }
    
    public function testIsLoggedInMirrorsToAuthServiceHasIdentity()
    {
        $auth = $this->getAuthMock();
        
        $auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        
        $helper = new Auth();
        $helper->setService($auth);
        
        $this->assertTrue($helper->isLoggedIn());
    }
    
    public function testHelperReturnsItselfWhenInvokedWithoutArguments()
    {
        $helper = new Auth();
        
        $this->assertSame($helper, $helper->__invoke());
    }
    
    public function testHelperReturnsNullIfNoIdentity()
    {
        
        $auth = $this->getAuthMock();
        $auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));
        
        $helper = new Auth();
        $helper->setService($auth);
        
        $this->assertNull($helper->__invoke('test'));
    }
    
    public function testHelperReturnsPropertyOfIdentityObject()
    {
        $auth = $this->getPropertyTestAuthMock();
        $helper = new Auth();
        $helper->setService($auth);
        
        $this->assertEquals('test', $helper('firstName'));
    }
    
    public function testHelperReturnsNullWhenInvalidPropertyIsRequested()
    {
        $auth = $this->getPropertyTestAuthMock();
        $helper = new Auth();
        $helper->setService($auth);
        
        $this->assertNull($helper('___invalid_property___'));
    }
}