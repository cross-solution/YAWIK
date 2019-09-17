<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace AuthTest\View\Helper;

use PHPUnit\Framework\TestCase;

use Auth\Entity\User;
use Auth\View\Helper\Auth as AuthHelper;

/**
 * Tests the Auth View Helper
 *
 * @covers \Auth\View\Helper\Auth
 * @coversDefaultClass \Auth\View\Helper\Auth
 * @group Auth
 * @group Auth.View
 * @group Auth.View.Helper
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class AuthTest extends TestCase
{
    public function testExtendsZfAbstractHelper()
    {
        $target = new AuthHelper();

        $this->assertInstanceOf('\Zend\View\Helper\AbstractHelper', $target);
    }

    /*
     * Test __call
     */

    /**
     * @covers ::__call
     */
    public function testProxiesMethodToAuthenticationService()
    {
        $auth = $this->getMockBuilder('\Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $auth->expects($this->once())
             ->method('getUser')
             ->willReturn(true);

        $target = new AuthHelper();
        $target->setService($auth);

        $this->assertTrue($target->getUser());
    }

    /**
     * @covers ::__call
     * @expectedException \DomainException
     * @expectedExceptionMessage Could not proxy
     */
    public function testThrowsExceptionIfTryingToProxyToUnknownMethod()
    {
        $auth = $this->getMockBuilder('\Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $target = new AuthHelper();
        $target->setService($auth);

        $target->callToUndefinedAuthServiceMethod();
    }

    public function testAuthenticationServiceCanBeSetAndRetrievedViaSetterAndGetterMethods()
    {
        $auth = $this->getMockBuilder('\Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $target = new AuthHelper();

        $this->assertSame($target, $target->setService($auth));
        $this->assertSame($auth, $target->getService());
    }

    public function testIsInvokable()
    {
        $target = new AuthHelper();

        $this->assertTrue(is_callable($target), '\Auth\View\Helper\Auth is not callable.');
    }

    public function testReturnsItselfWhenInvokedViaObjectInvokationAndNoArgumentIsPassed()
    {
        $target = new AuthHelper();

        $this->assertSame($target, $target());
    }

    public function testReturnsUserPropertiesWhenInvokedViaObjectInvokationAndAnArgumentIsPassed()
    {
        $login = 'userLogin';

        $user = new User();
        $user->setLogin($login);

        $auth = $this->getMockBuilder('Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $auth->expects($this->exactly(2))
             ->method('getUser')
             ->willReturn($user);

        $target = new AuthHelper();
        $target->setService($auth);

        $this->assertEquals($login, $target('login'));
        $this->assertNull($target('nonexistent'));
    }

    public function testIsAbleToCheckIfAUserIsCurrentlyLoggedIn()
    {
        $auth = $this->getMockBuilder('Auth\AuthenticationService')
                     ->disableOriginalConstructor()
                     ->getMock();

        $auth->expects($this->exactly(2))
             ->method('hasIdentity')
             ->will($this->onConsecutiveCalls(true, false));

        $target = new AuthHelper();
        $target->setService($auth);

        $this->assertTrue($target->isLoggedIn());
        $this->assertFalse($target->isLoggedIn());
    }
}
