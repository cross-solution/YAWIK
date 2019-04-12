<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace AuthTest\Listener;

use PHPUnit\Framework\TestCase;

use Auth\Listener\TokenListener;
use PHPUnit\Framework\TestResult;
use Zend\Mvc\MvcEvent;

/**
 * Test for TokenListener
 *
 * @covers \Auth\Listener\TokenListener
 * @group Auth
 * @group Auth.Listeners
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class TokenListenerTest extends TestCase
{
    # http://matthewturland.com/2010/08/19/process-isolation-in-phpunit/
    # added, because these tests are failing on travis
    public function run(TestResult $result = null): TestResult
    {
        $this->setPreserveGlobalState(false);
        return parent::run($result);
    }

    public function testWorksAsSharedListenerAggregate()
    {
        $target = new TokenListener();
        $expId = 'Zend\Mvc\Application';
        $expEvent = MvcEvent::EVENT_BOOTSTRAP;
        $expCallback = array($target, 'onBootstrap');
        $expPriority = 1000;


        $callback = $this->getMockBuilder('\Zend\Stdlib\CallbackHandler')
                         ->disableOriginalConstructor()
                         ->getMock();

        $events = $this->getMockBuilder('\Zend\EventManager\SharedEventManagerInterface')
                       ->setMethods(array('attach'))
                       ->getMockForAbstractClass();

        $events->expects($this->once())
               ->method('attach')
               ->with($expId, $expEvent, $expCallback, $expPriority)
               ->willReturn($expCallback);

        $events->expects($this->once())
               ->method('detach')
               ->with($expCallback, 'Zend\Mvc\Application')
               ->willReturn(true);


        $target->attachShared($events);

        $this->assertAttributeEquals($expCallback, 'listener', $target);

        $target->detachShared($events);

        $this->assertAttributeEquals(null, 'listener', $target);
    }

    public function provideRequestParameterTestData()
    {
        return array(
            array('post', '1234'),
            array('query', '1234'),
        );
    }

    /**
     * @dataProvider provideRequestParameterTestData
     * @runInSeparateProcess
     */
    public function testSetsSessionIdIfAuthParameterIsPassedWhenCalledAsListener($type, $token)
    {
        $event = $this->getMvcEventMock($type, array('auth' => $token));
        $target = new TokenListener();

        $target->onBootstrap($event);
        $this->assertEquals($token, session_id());
    }

    /**
     * @dataProvider provideRequestParameterTestData
     * @runInSeparateProcess
     */
    public function testSetsSessionParameterIfTokenParameterIsPassedWhenCalledAsListener($type, $token)
    {
        $event = $this->getMvcEventMock($type, array('token' => $token));
        $target = new TokenListener();

        $target->onBootstrap($event);

        $session = new \Zend\Session\Container('Auth');
        $this->assertEquals($token, $session->token);
    }

    private function getMvcEventMock($type, $params)
    {
        $request = new \Zend\Http\Request();
        $params  = new \Zend\Stdlib\Parameters($params);
        $method  = 'post' == $type ? 'setPost' : 'setQuery';

        $request->$method($params);

        $event = new \Zend\Mvc\MvcEvent();
        $event->setRequest($request);

        return $event;
    }
}
