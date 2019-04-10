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

use Auth\Listener\SocialProfilesUnconfiguredErrorListener;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

/**
 * Tests for \Auth\Listener\SocialProfilesUnconfiguredErrorListener
 *
 * @covers \Auth\Listener\SocialProfilesUnconfiguredErrorListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class SocialProfilesUnconfiguredErrorListenerTest extends TestCase
{

    /**
     * @testdox Extends \Zend\EventManager\AbstractListenerAggregate
     */
    public function testExtendsAbstractListenerAggregate()
    {
        $target = new SocialProfilesUnconfiguredErrorListener();

        $this->assertInstanceOf('Zend\EventManager\AbstractListenerAggregate', $target);
    }

    public function testAttachsToDispatchErrorEvent()
    {
        $target = new SocialProfilesUnconfiguredErrorListener();

        $events = $this->getMockBuilder('\Zend\EventManager\EventManager')->disableOriginalConstructor()->getMock();
        $events->expects($this->once())
               ->method('attach')->with(MvcEvent::EVENT_DISPATCH_ERROR, [ $target, 'onDispatchError' ])
               ->willReturn('worked');

        $this->assertSame($target, $target->attach($events), 'Fluent interface broken');
        $this->assertAttributeEquals(['worked'], 'listeners', $target);
    }

    public function provideTestData()
    {
        return [
           [ true, true, true, true ],
           [ false, true, true, false ],
           [ true, false, true, false ],
           [ true, true, false, false ],
           [ false, false, true, false ],
           [ false, true, false, false ],
           [ true, false, false, false ],
           [ false, false, false, false ],
       ];
    }

    /**
     * @dataProvider provideTestData
     *
     * @param $useValidException
     * @param $useValidModel
     * @param $useValidError
     * @param $shouldModelChange
     */
    public function testSetsViewModelTemplateIfPrerequisitesAreMet($useValidException, $useValidModel, $useValidError, $shouldModelChange)
    {
        $target = new SocialProfilesUnconfiguredErrorListener();

        $exception = new \Exception($useValidException ? 'Your application id and secret' : 'This exception must not match');

        $event = $this->getMockBuilder('\Zend\Mvc\MvcEvent')->disableOriginalConstructor()->getMock();

        $event->expects($this->once())
              ->method('getParam')->with('exception')
              ->willReturn($exception);

        if ($useValidModel) {
            $model = $this->getMockBuilder('\Zend\View\Model\ViewModel')->disableOriginalConstructor()->getMock();
            if ($shouldModelChange) {
                $model->expects($this->once())->method('setTemplate')->with('auth/error/social-profiles-unconfigured');
            } else {
                $model->expects($this->never())->method('setTemplate');
            }
            $event->expects($this->once())
                  ->method('getError')
                  ->willReturn($useValidError ? Application::ERROR_EXCEPTION : 'NotMatchError');
        } else {
            $model = 'not a view model instance';
            $event->expects($this->never())->method('getError');
        }

        $event->expects($this->once())
              ->method('getResult')
              ->willReturn($model);

        $target->onDispatchError($event);
    }
}
