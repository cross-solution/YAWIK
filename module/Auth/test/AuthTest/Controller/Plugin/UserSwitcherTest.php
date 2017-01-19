<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace AuthTest\Controller\Plugin;

use Auth\AuthenticationService;
use Auth\Controller\Plugin\UserSwitcher;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

/**
 * Tests for \Auth\Controller\Plugin\UserSwitcher
 * 
 * @covers \Auth\Controller\Plugin\UserSwitcher
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Auth
 * @group Auth.Controller
 * @group Auth.Controller.Plugin
 */
class UserSwitcherTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|UserSwitcher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        UserSwitcher::class,
        'getSimpleAuthMock',
        '@testInheritance' => [ 'as_reflection' => true ],
        '@testInvokationProxiesToCorrectMethods' => [
            'args' => false,
            'mock' => [ 'clear' => 1, 'switchUser' => ['with' => 'testUserId', 'count' => 1]],
        ],
        '@testClearRestoresOriginalUserAndClearsSession' => [
            'args' => 'getComplexAuthMock',
        ],
        '@testSwitchUserExchangeOriginalUserAndStoresSession' => [
            'args' => 'getComplexAuthMock',
        ],
    ];

    private $inheritance = [ AbstractPlugin::class ];

    private function getSimpleAuthMock()
    {
        $auth = $this
            ->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();

        return [ $auth ];
    }

    private function getComplexAuthMock()
    {
        $auth = $this
            ->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->setMethods(['clearIdentity', 'getStorage'])
            ->getMock();


        $storage = $this
            ->getMockBuilder(StorageInterface::class)
            ->setMethods(['read', 'write'])
            ->getMockForAbstractClass();

        $storage->expects($this->once())->method('read')->willReturn('originalUser');
        $storage->expects($this->once())->method('write')->with('switchedUser');

        $auth->expects($this->once())->method('getStorage')->willReturn($storage);
        $auth->expects($this->once())->method('clearIdentity');

        return [ $auth ];
    }

    public function testInvokationProxiesToCorrectMethods()
    {
        $this->target->__invoke();
        $this->target->__invoke('testUserId');
    }

    public function testClearReturnsEarlyWhenNoSwitchedUserIsSet()
    {
        $this->assertFalse($this->target->clear());
    }

    public function testClearRestoresOriginalUserAndClearsSession()
    {
        $session = new Container(UserSwitcher::SESSION_NAMESPACE);
        $session->isSwitchedUser = true;
        $session->originalUser = 'switchedUser';

        $this->assertTrue($this->target->clear());
        $this->assertArrayNotHasKey(UserSwitcher::SESSION_NAMESPACE, $_SESSION);
    }

    public function testSwitchUserReturnsEarlyWhenSwitchedUserIsSet()
    {
        $_SESSION[UserSwitcher::SESSION_NAMESPACE]['isSwitchedUser'] = true;

        $this->assertFalse($this->target->switchUser('switchedUser'));

        $_SESSION = [];
    }

    public function testSwitchUserExchangeOriginalUserAndStoresSession()
    {
        $this->assertTrue($this->target->switchUser('switchedUser'));
        $this->assertEquals(
             ['isSwitchedUser' => true, 'originalUser' => 'originalUser'],
             $_SESSION[UserSwitcher::SESSION_NAMESPACE]->getArrayCopy()
        );

        $_SESSION = [];
    }
}