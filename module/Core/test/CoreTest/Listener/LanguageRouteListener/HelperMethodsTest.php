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

use Auth\AuthenticationService;
use Auth\Entity\UserInterface;
use Core\Listener\LanguageRouteListener;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use Locale;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests the helper methods of \Core\Listener\LanguageRouteListener
 * 
 * @covers \Core\Listener\LanguageRouteListener
 * @coversDefaultClass \Core\Listener\LanguageRouteListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Listener
 * @group Core.Listener.LanguageRouteListener
 */
class HelperMethodsTest extends \PHPUnit_Framework_TestCase
{
    use ServiceManagerMockTrait;

    public function testIsSupportedLanguage()
    {
        $target = new LrlMock();
        $target->setSupportedLanguages(['xx' => 'xx_XX']);

        $this->assertTrue($target->isSupportedLanguage('xx'));
        $this->assertFalse($target->isSupportedLanguage('yy'));
    }

    public function testRedirect()
    {
        $response = new Response();
        $target = new LrlMock();
        $actual = $target->redirect($response, '/some/uri');

        $this->assertSame($response, $actual);
        $this->assertEquals(302, $actual->getStatusCode());
        $this->assertEquals('/some/uri', (string) $actual->getHeaders()->get('Location')->getUri());
    }

    public function testSetLocale()
    {
        $application = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
        $services    = $this->getMockBuilder(ServiceManager::class)->disableOriginalConstructor()->getMock();
        $translator  = $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
        $router      = $this->getMockBuilder(SimpleRouteStack::class)->disableOriginalConstructor()->getMock();
        $routeMatch  = $this->getMockBuilder(RouteMatch::class)->disableOriginalConstructor()->getMock();
        $event       = $this->getMockBuilder(MvcEvent::class)->getMock();

        $event->expects($this->once())->method('getApplication')->willReturn($application);
        $event->expects($this->once())->method('getRouteMatch')->willReturn($routeMatch);
        $event->expects($this->once())->method('getRouter')->willReturn($router);

        $application->expects($this->once())->method('getServiceManager')->willReturn($services);

        $services->expects($this->once())->method('get')->with('translator')->willReturn($translator);

        $translator->expects($this->once())->method('setLocale')->with('xx_XX');

        $router->expects($this->once())->method('setDefaultParam')->with('lang', 'xx');

        $routeMatch->expects($this->once())->method('getParam')->with('lang')->willReturn(null);
        $routeMatch->expects($this->once())->method('setParam')->with('lang', 'xx');

        $target = new LrlMock();
        $target->setSupportedLanguages(['xx' => 'xx_XX']);
        $target->setLocale($event, 'xx');

    }

    private function getEventMock($hasIdentity, $lang = null, $hasHeaders = true)
    {
        $event = $this
            ->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getApplication', 'getRequest'])
            ->getMock()
        ;

        $application = $this
            ->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServiceManager'])
            ->getMock()
        ;

        $auth = $this
            ->getMockBuilder(AuthenticationService::class)
            ->setMethods(['hasIdentity', 'getUser'])
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $services = $this->getServiceManagerMock([
            'AuthenticationService' => $auth,
        ]);

        if ($hasIdentity) {
            $user = $this->getMockBuilder(UserInterface::class)->getMockForAbstractClass();
            $settings = new \stdClass();
            $settings->language = $lang;
            $auth->expects($this->once())->method('getUser')->willReturn($user);
            $user->expects($this->once())->method('getSettings')->willReturn($settings);
        } else {
            $auth->expects($this->never())->method('getUser');
        }

        $auth->expects($this->once())->method('hasIdentity')->willReturn($hasIdentity);
        $event->expects($this->once())->method('getApplication')->willReturn($application);
        $application->expects($this->once())->method('getServiceManager')->willReturn($services);

        if (!$lang) {
            $request = new Request();
            if ($hasHeaders) {
                $request->getHeaders()->addHeaderline('Accept-Language', 'xx');
            }

            $event->expects($this->once())->method('getRequest')->willReturn($request);
        }
        return $event;

    }

    public function testDetectLanguageWithLoggedInUserAndLanguageSet()
    {
        $event = $this->getEventMock(true, 'uu');
        $target = new LrlMock();

        $actual = $target->detectLanguage($event);

        $this->assertSame('uu', $actual);


    }

    public function testDetectLanguageWithLoggedInUserAndNoLanguageSet()
    {
        $event = $this->getEventMock(true, null, false);
        $target = new LrlMock();
        $target->setSupportedLanguages(['yy' => 'yy_YXX']);

        $actual = $target->detectLanguage($event);

        $this->assertSame('yy', $actual);
    }

    public function testDetectLanguageWithAcceptedLanguage()
    {
        $event = $this->getEventMock(false, null, true);

        $target = new LrlMock();
        $target->setSupportedLanguages(['yy' => 'yy_YY', 'xx' => 'xx_XX']);

        $this->assertSame('xx', $target->detectLanguage($event));
    }

    public function testDetectLanguageWithoutAcceptedLanguage()
    {
        $event = $this->getEventMock(false, null, true);

        $target = new LrlMock();
        $target->setSupportedLanguages(['yy' => 'yy_YY']);

        $this->assertSame('yy', $target->detectLanguage($event));
    }

}

class LrlMock extends LanguageRouteListener
{
    public function isSupportedLanguage($lang)
    {
        return parent::isSupportedLanguage($lang);
    }

    public function setLocale(MvcEvent $e, $lang)
    {
        $origLocale = setlocale(LC_ALL, "0");
        parent::setLocale($e, $lang);
        setlocale(LC_ALL, $origLocale);
    }


    public function redirect($response, $uri)
    {
        return parent::redirect($response, $uri);
    }

    public function detectLanguage(MvcEvent $e)
    {
        return parent::detectLanguage($e);
    }
}


