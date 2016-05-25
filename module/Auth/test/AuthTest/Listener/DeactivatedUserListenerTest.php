<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
namespace AuthTest\Listener;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManager;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\ApplicationInterface as Application;
use Zend\ServiceManager\ServiceLocatorInterface as ServiceManager;
use Zend\Authentication\AuthenticationServiceInterface as AuthenticationService;
use Auth\Entity\User;
use Zend\Http\PhpEnvironment\Response;
use Auth\Listener\DeactivatedUserListener as Listener;

/**
 * @covers \Auth\Listener\DeactivatedUserListener
 */
class DeactivatedUserListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Listener
     */
    protected $listener;
    
    /**
     * @var User
     */
    protected $user;
    
    /**
     * @var AuthenticationService
     */
    protected $auth;
    
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @var Application
     */
    protected $application;
    
    /**
     * @var MvcEvent
     */
    protected $event;
    
    /**
     * @var RouteMatch
     */
    protected $routeMatch;
    
    public function setUp()
    {
        $this->listener = new Listener();
        $this->user = $this->getMock(User::class, ['isActive']);
        
        $this->auth = $this->getMock(AuthenticationService::class, ['authenticate', 'hasIdentity', 'getIdentity', 'clearIdentity', 'getUser']);
        $this->auth->expects($this->any())
            ->method('getUser')
            ->willReturn($this->user);
        
        $this->serviceManager = $this->getMock(ServiceManager::class, ['get', 'has']);
        $this->serviceManager->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name)
            {
                switch ($name)
                {
                    case 'AuthenticationService':
                        return $this->auth;
                    break;
                    
                    default:
                        throw new \InvalidArgumentException();
                    break;
                }
            }));
        
        $this->application = $this->getMock(Application::class, ['getServiceManager', 'getRequest', 'getResponse', 'run', 'getEventManager']);
        $this->application->expects($this->any())
            ->method('getServiceManager')
            ->willReturn($this->serviceManager);
        
        $this->routeMatch = new RouteMatch([]);
        
        $this->event = $this->getMock(MvcEvent::class);
        $this->event->expects($this->any())
            ->method('getApplication')
            ->willReturn($this->application);
        
        $this->event->expects($this->any())
            ->method('getRouteMatch')
            ->willReturn($this->routeMatch);
    }
    
    public function testInstance()
    {
        $this->assertInstanceOf(ListenerAggregateInterface::class, $this->listener);
    }
    
    public function testAttach()
    {
        $eventManager = $this->getMock(EventManager::class);
        $eventManager->expects($this->exactly(3))
            ->method('attach')
            ->withConsecutive(
                 [$this->equalTo(MvcEvent::EVENT_DISPATCH_ERROR), $this->identicalTo([$this->listener, 'prepareExceptionViewModel'], $this->identicalTo(null))],
                 [$this->equalTo(MvcEvent::EVENT_RENDER_ERROR), $this->identicalTo([$this->listener, 'prepareExceptionViewModel'], $this->identicalTo(null))],
                 [$this->equalTo(MvcEvent::EVENT_ROUTE), $this->identicalTo([$this->listener, 'checkDeactivatedUser'], $this->identicalTo(null))]
             );
        
        $this->listener->attach($eventManager);
    }
    
    /**
     * @dataProvider dataForheckDeactivatedUser
     */
    public function testCheckDeactivatedUser($routeName, $hasIdentity, $isActive, $expectedTriggerCalled)
    {
        $this->routeMatch->setMatchedRouteName($routeName);
        $this->auth->expects($this->any())
            ->method('hasIdentity')
            ->willReturn($hasIdentity);
        $this->user->expects($this->any())
            ->method('isActive')
            ->willReturn($isActive);
        $this->event->expects($expectedTriggerCalled ? $this->once() : $this->never())
            ->method('setError');
        
        if ($expectedTriggerCalled) {
            $eventManager = $this->getMock(EventManager::class);
            $eventManager->expects($this->once())
                ->method('trigger')
                ->willReturn(new \Zend\EventManager\ResponseCollection())
                ->with($this->equalTo(MvcEvent::EVENT_DISPATCH_ERROR));
    
            $target = $this->getMock(\stdClass::class, ['getEventManager']);
            $target->expects($this->any())
                ->method('getEventManager')
                ->willReturn($eventManager);
            
            $this->event->expects($this->once())
                ->method('getTarget')
                ->willReturn($target);
        }
        
        $this->listener->checkDeactivatedUser($this->event);
    }

    public function dataForheckDeactivatedUser()
    {
        return [
            [
                '', true, false, 1
            ],
            [
                'auth-logout', true, false, 0
            ],
            [
                '', false, false, 0
            ],
            [
                '', true, true, 0
            ],
        ];
    }
    
    /**
     * @dataProvider dataForPrepareExceptionViewModel
     */
    public function testPrepareExceptionViewModel($error, $result, $exception, $hasIdentity, $isActive, $expectedSetResultCalled)
    {
        $this->event->expects($this->any())
            ->method('getError')
            ->willReturn($error);
        $this->event->expects($this->any())
            ->method('getResult')
            ->willReturn($result);
        $this->event->expects($this->any())
            ->method('getParam')
            ->with($this->equalTo('exception'))
            ->willReturn($exception);
        $this->auth->expects($this->any())
            ->method('hasIdentity')
            ->willReturn($hasIdentity);
        $this->user->expects($this->any())
            ->method('isActive')
            ->willReturn($isActive);
        $this->event->expects($this->exactly($expectedSetResultCalled))
            ->method('setResult');
        
        $this->listener->prepareExceptionViewModel($this->event);
    }

    public function dataForPrepareExceptionViewModel()
    {
        $exception = new \Auth\Exception\UserDeactivatedException();
        
        return [
            [
                'error', null, $exception, true, false, 1
            ],
            [
                '', null, $exception, true, false, 0
            ],
            [
                'error', new Response(), $exception, true, false, 0
            ],
            [
                'error', null, null, true, false, 0
            ],
            [
                'error', null, $exception, false, false, 0
            ],
            [
                'error', null, $exception, true, true, 0
            ],
        ];
    }
}
