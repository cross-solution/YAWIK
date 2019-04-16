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

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

use Core\Options\ModuleOptions;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Core\Listener\LanguageRouteListener;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use Zend\Router\RouteStackInterface;
use Core\I18n\Locale as LocaleService;

/**
 * Tests the listener callbacks for \Core\Listener\LanguageRouteListener
 *
 * @covers \Core\Listener\LanguageRouteListener::onDispatchError()
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @group Core
 * @group Core.Listener
 * @group Core.Listener.LanguageRouteListener
 */
class OnDispatchErrorCallbackTest extends TestCase
{
    use SetupTargetTrait;

    private $target = [
        LanguageRouteListener::class,
        'mock' => [ 'detectLanguage' => ['return' => 'xx'], 'setLocale', 'isSupportedLanguage', 'redirect'],
        'args' => 'getConstructorArgs'
    ];

    /**
     * @var ModuleOptions
     */
    private $moduleOptions;

    public function testHandleConsoleRequests()
    {
        $event = $this
           ->getMockBuilder(MvcEvent::class)
           ->disableOriginalConstructor()
           ->setMethods(['getRequest', 'getError'])
           ->getMock()
       ;

        $request = new \Zend\Console\Request();

        $event->expects($this->once())->method('getRequest')->willReturn($request);
        $event->expects($this->never())->method('getError');

        $this->assertNull($this->target->onDispatchError($event));
    }

    public function testHandleNonRouteMatchErrors()
    {
        $event = $this
            ->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequest', 'getError'])
            ->getMock()
        ;

        $request = new \Zend\Http\PhpEnvironment\Request();

        $event->expects($this->once())->method('getRequest')->wilLReturn($request);
        $event->expects($this->once())->method('getError')->willReturn('MUSTHANDLETHISERROR');

        $this->assertNull($this->target->onDispatchError($event));
    }

    private function getEventMock($baseUrl, $requestUri)
    {
        $event = $this
            ->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequest', 'getError', 'getRouter', 'stopPropagation'])
            ->getMock()
        ;

        $this->router = $this
            ->getMockBuilder(RouteStackInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBaseUrl', 'match'])
            ->getMockForAbstractClass()
        ;

        $request = new \Zend\Http\PhpEnvironment\Request();

        $event->expects($this->atLeastOnce())->method('getRequest')->wilLReturn($request);
        $event->expects($this->once())->method('getError')->willReturn(Application::ERROR_ROUTER_NO_MATCH);

        $event->expects($this->once())->method('getRouter')->willReturn($this->router);

        $this->router->expects($this->once())->method('getBaseUrl')->willReturn($baseUrl);

        $request->setRequestUri($baseUrl.$requestUri);

        return $event;
    }

    public function testHandleLanguageUriWithSupportedLanguage()
    {
        $event = $this->getEventMock('/base/uri', '/xx/route');

        $this->target->expects($this->once())->method('setLocale')->with($event, 'xx');

        $this->target->onDispatchError($event);
    }

    public function testHandleLanguageUriWithoutSupportedLanguage()
    {
        $event = $this->getEventMock('/base/uri', '/yy/route');
        $response = new Response();
        $event->setResponse($response);
        $this->target->expects($this->once())->method('setLocale')->with($event, 'xx');

        $this->target->onDispatchError($event);
    }

    public function testHandleNonLanguageUriThatCanBeMappedToLanguageUri()
    {
        $event = $this->getEventMock('/base/uri', '/see/no/lang');

        $event->expects($this->once())
              ->method('stopPropagation')
              ->with(true)
        ;
        $response = new Response();
        $event->setResponse($response);
        $routeMatch = new RouteMatch([]);
        $this->router
            ->expects($this->once())
            ->method('match')
            ->with($this->callback(
                function ($v) {
                    Assert::assertEquals('/base/uri/xx/see/no/lang', (string) $v->getUri());
                    return true;
                }
              ))
            ->willReturn($routeMatch)
        ;

        $this->target
            ->expects($this->once())
            ->method('redirect')
            ->with($response, '/base/uri/xx/see/no/lang')
        ;
        $this->target
            ->expects($this->never())
            ->method('setLocale')
        ;

        $this->target->onDispatchError($event);
    }

    public function testHandleNonLanguageUriThatCannotBeMapped()
    {
        $event = $this->getEventMock('/base/uri', '/non/mappable/route');

        $response = new Response();
        $event->setResponse($response);
        $routeMatch = null;
        $this->router
            ->expects($this->once())
            ->method('match')
            ->with($this->callback(
                function ($v) {
                    Assert::assertEquals('/base/uri/xx/non/mappable/route', (string) $v->getUri());
                    return true;
                }
                ))
            ->willReturn($routeMatch);

        $this->target->expects($this->never())->method('redirect');
        $this->target->expects($this->once())->method('setLocale')->with($event, 'xx');

        $this->target->onDispatchError($event);
    }

    public function testHandleWhenDetectLanguageDisabled()
    {
        $event = $this->getEventMock('/base/uri', '/see/no/lang');

        $event->expects($this->once())
            ->method('stopPropagation')
            ->with(true)
        ;
        $response = new Response();
        $event->setResponse($response);

        $routeMatch = new RouteMatch([]);
        $this->router
            ->expects($this->once())
            ->method('match')
            ->with($this->callback(
                function ($v) {
                    Assert::assertEquals('/base/uri/default/see/no/lang', (string) $v->getUri());
                    return true;
                }
            ))
            ->willReturn($routeMatch)
        ;


        $target = $this->target;
        $this->moduleOptions->setDefaultLanguage('default');
        $this->moduleOptions->setDetectLanguage(false);

        $target->expects($this->once())
            ->method('redirect')
            ->with($response, '/base/uri/default/see/no/lang')
        ;

        $target->onDispatchError($event);
    }

    public function getConstructorArgs()
    {
        $this->moduleOptions = new ModuleOptions();
        return [new LocaleService([]),$this->moduleOptions];
    }
}
