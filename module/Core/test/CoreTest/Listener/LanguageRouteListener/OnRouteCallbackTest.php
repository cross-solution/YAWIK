<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Listener\LanguageRouteListener;

use Core\Options\ModuleOptions;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Zend\EventManager\EventManager;
use Core\Listener\LanguageRouteListener;
use Zend\EventManager\ResponseCollection;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Core\I18n\Locale as LocaleService;

/**
 * Tests the listener callbacks for \Core\Listener\LanguageRouteListener
 *
 * @covers \Core\Listener\LanguageRouteListener::onRoute()
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @group Core
 * @group Core.Listener
 * @group Core.Listener.LanguageRouteListener
 */
class OnRouteCallbackTest extends \PHPUnit_Framework_TestCase
{
    use SetupTargetTrait;

    private $target = [
        LanguageRouteListener::class,
        'mock' => [ 'detectLanguage' => ['return' => 'xx'], 'setLocale' ],
        'args' => 'getConstructorArgs'
    ];
    
    protected $localeService;

    private function getEventMock($routeName, $lang = null)
    {
        $routeMatch = $this
            ->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMatchedRouteName', 'getParam'])
            ->getMock()
        ;

        $routeMatch->expects($this->once())->method('getMatchedRouteName')->willReturn($routeName);
        if ($lang) {
            $routeMatch->expects($this->once())->method('getParam')->with('lang')->willReturn($lang);
        }

        $event = $this
            ->getMockBuilder(MvcEvent::class)
            ->setMethods(['getRouteMatch', 'setError', 'setTarget', 'getApplication'])
            ->getMock()
        ;

        $event->expects($this->once())->method('getRouteMatch')->willReturn($routeMatch);

        return $event;
    }

    public function testNoLanguageRoute()
    {
        $event = $this->getEventMock('some/route/wo/language');

        $this->target->expects($this->once())->method('setLocale')->with($event, 'xx');

        $this->target->onRoute($event);
    }

    public function testRouteWithSupportedLanguage()
    {
        $event = $this->getEventMock('lang/route', 'supported');
        $this->localeService->expects($this->once())->method('isLanguageSupported')->with('supported')->willReturn(true);

        $this->target->expects($this->once())->method('setLocale')->with($event, 'supported');

        $this->target->onRoute($event);
    }

    public function testRouteWithoutSupportedLanguage()
    {
        $event = $this->getEventMock('lang/route', 'unsupported');

        $event->expects($this->once())->method('setError')->with(Application::ERROR_ROUTER_NO_MATCH);
        $event->expects($this->once())->method('setTarget')->with($this->target);

        $result = $this->getMockBuilder(ResponseCollection::class)->setMethods(['last'])->getMock();

        $result->expects($this->once())->method('last')->willReturn('response');

        $events = $this
            ->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['trigger'])
            ->getMock()
        ;

        $events->expects($this->once())->method('trigger')->with(MvcEvent::EVENT_DISPATCH_ERROR, $event)->willReturn($result);

        $application = $this
            ->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEventManager'])
            ->getMock()
        ;

        $application->expects($this->once())->method('getEventManager')->willReturn($events);

        $event->expects($this->once())->method('getApplication')->willReturn($application);

        $actual = $this->target->onRoute($event);

        $this->assertEquals('response', $actual);
    }
    
    public function getConstructorArgs()
    {
        $this->localeService = $this->getMockBuilder(LocaleService::class)
            ->setConstructorArgs([[]])
            ->setMethods(['isLanguageSupported'])
            ->getMock();

        return [$this->localeService, new ModuleOptions()];
    }
}


