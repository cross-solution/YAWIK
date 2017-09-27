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

use Acl\Controller\Plugin\Acl;
use Auth\AuthenticationService;
use Auth\Controller\Plugin\UserSwitcher;
use Auth\Entity\User;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
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
    use TestInheritanceTrait, TestSetterGetterTrait;

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
        '@testSwitchUserUsesUserIdFromProvidedUserInterface' => [
            'args' => 'getComplexAuthMock',
        ],
        '@testSwitchUserSetsUserOnAclPluginIfProvided' => [
            'args' => 'getComplexAuthMock',
        ],
    ];

    private $inheritance = [ AbstractPlugin::class ];

    public function propertiesProvider()
    {
        $createSession = function() { $_SESSION[UserSwitcher::SESSION_NAMESPACE]['params'] = ['param' => 'value']; };
        $clearSession = function() { $_SESSION = []; };
        return [
            ['sessionParams', [
                'pre' => $createSession,
                'post' => $clearSession,
                'ignore_setter' => true,
                'value' => ['param' => 'value']
            ]],
            ['sessionParams', [
                'post' => $clearSession,
                'ignore_setter' => true,
                'value' => [],
            ]],
            ['sessionParams', [
                'pre' => $createSession,
                'post' => $clearSession,
                'value' => ['new' => 'another'],
                'setter_args' => [/*merge*/ true],
                'expect' => ['param' => 'value', 'new' => 'another'],
            ]],
            ['sessionParam', [
                'post' => $clearSession,
                'default' => 'default',
                'default_args' => ['param', 'default'],
                'setter_args' => ['value'],
                'getter_args' => ['param'],
                'value' => 'param',
                'expect' => 'value',
            ]],
            ['aclPlugin', ['value' => $this->getMockBuilder(Acl::class)->disableOriginalConstructor()->getMock(), 'default' => null]]
        ];
    }

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
            ->setMethods(['clearIdentity', 'getStorage', 'getUser'])
            ->getMock();


        $storage = $this
            ->getMockBuilder(StorageInterface::class)
            ->setMethods(['read', 'write'])
            ->getMockForAbstractClass();

        $storage->expects($this->any())->method('read')->willReturn('originalUser');
        $storage->expects($this->any())->method('write')->with('switchedUser');

        $auth->expects($this->any())->method('getStorage')->willReturn($storage);
        $auth->expects($this->any())->method('clearIdentity');

        $auth->expects($this->any())->method('getUser')->willReturn(new User());

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

    public function returnReferenceProvider()
    {
        return [
            [ null ], [ 'some/ref/uri' ],
        ];
    }

    /**
     * @dataProvider returnReferenceProvider
     *
     * @param $ref
     */
    public function testClearRestoresOriginalUserAndClearsSession($ref)
    {
        $session = new Container(UserSwitcher::SESSION_NAMESPACE);
        $session->isSwitchedUser = true;
        $session->originalUser = 'switchedUser';
        $oldSession      = [
            'oldSession' => true,
            'must'       => 'be same'
        ];
        if (null !== $ref) {
            $session->ref = $ref;
        }
        $session->session = serialize($oldSession);

        if (null === $ref) {
            $this->assertTrue($this->target->clear());
        } else {
            $this->assertEquals($ref, $this->target->clear());
        }
        $this->assertEquals($oldSession, $_SESSION);

    }

    public function testSwitchUserReturnsEarlyWhenSwitchedUserIsSet()
    {
        $_SESSION[UserSwitcher::SESSION_NAMESPACE]['isSwitchedUser'] = true;

        $this->assertFalse($this->target->switchUser('switchedUser'));

        $_SESSION = [];
    }

    public function testSwitchUserExchangeOriginalUserAndStoresSession()
    {
        $request = new Request();
        $request->setRequestUri('/some/ref');

        $controller = $this->getMockBuilder(AbstractActionController::class)->disableOriginalConstructor()->getMock();
        $controller->expects($this->once())->method('getRequest')->willReturn($request);

        $this->target->setController($controller);
        $oldSession = [
            'this is' => 'the old session'
        ];
        $_SESSION = $oldSession;
        $this->assertTrue($this->target->switchUser('switchedUser'));
        $this->assertEquals(
             [
                 'isSwitchedUser' => true,
                 'originalUser' => 'originalUser',
                 'params' => [], 'ref' => '/some/ref',
                 'session' => serialize($oldSession)
             ],
             $_SESSION[UserSwitcher::SESSION_NAMESPACE]->getArrayCopy()
        );
        $_SESSION = [];
    }

    public function testSwitchUserUsesUserIdFromProvidedUserInterface()
    {
        $_SESSION = [];
        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()
            ->setMethods(['getId'])->getMock();
        $user->expects($this->once())->method('getId')->willReturn('switchedUser');

        $this->assertTrue($this->target->switchUser($user, ['ref' => 'ref']));
        $this->assertEquals($_SESSION[UserSwitcher::SESSION_NAMESPACE]['ref'], 'ref');
        $_SESSION = [];
    }

    public function testSwitchUserSetsUserOnAclPluginIfProvided()
    {
        $_SESSION = [];
        $acl = $this->getMockBuilder(Acl::class)->disableOriginalConstructor()->setMethods(['setUser'])->getMock();
        $acl->expects($this->once())->method('setUser')->with($this->isInstanceOf(User::class));

        $user = $this->getMockBuilder(User::class)->disableOriginalConstructor()
                     ->setMethods(['getId'])->getMock();
        $user->expects($this->any())->method('getId')->willReturn('switchedUser');

        $this->target->switchUser($user, ['ref' => 'ref']);
        $_SESSION = [];
        $this->target->setAclPlugin($acl);
        $this->target->switchUser($user, ['ref' => 'ref']);

        $_SESSION = [];
    }

    public function testIsSwitchedUser()
    {
        $this->assertFalse($this->target->isSwitchedUser());

        $_SESSION[UserSwitcher::SESSION_NAMESPACE]['isSwitchedUser'] = true;

        $this->assertTrue($this->target->isSwitchedUser());

        $_SESSION = [];
    }
}
