<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Navigation;

use Core\Factory\Navigation\DefaultNavigationFactory;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\Router\RouteMatch;

/**
 * Tests for \Core\Factory\Navigation\DefaultNavigationFactory
 * 
 * @covers \Core\Factory\Navigation\DefaultNavigationFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *  
 */
class DefaultNavigationFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var DefaultNavigationFactory|array
     */
    private $target = DefaultNavigationFactory::class;

    private $inheritance = [ '\Zend\Navigation\Service\DefaultNavigationFactory' ];

    public function testSetsActiveFlagOnPagesProvidingTheActiveOnOption()
    {
        $pages = [
            'page1' => [
                'active_on' => 'matchedRouteName',
            ],
            'page2' => [
                'active_on' => 'notMatchedRoute',
            ],
            'page3' => [
                'active_on' => [ 'matchedRouteName', 'anotherRoute' ],
            ],
            'page4' => []
        ];

        $routeMatch = new RouteMatch([]);
        $routeMatch->setMatchedRouteName('matchedRouteName');

        $expect = $pages;
        $expect['page1']['active'] = true;
        $expect['page3']['active'] = true;

        $m = new \ReflectionMethod($this->target, 'injectComponents');
        $m->setAccessible(true);

        $actual = $m->invoke($this->target, $pages, $routeMatch);

        $this->assertEquals($expect, $actual);
    }

    public function testDoesNothingIfNoRouteMatchIsPassed()
    {
        $pages = [
            'page1' => [
                'active_on' => 'matchedRouteName',
            ],
            'page2' => [
                'active_on' => 'notMatchedRoute',
            ],
            'page3' => [
                'active_on' => [ 'matchedRouteName', 'anotherRoute' ],
            ],
            'page4' => []
        ];

        $expect = $pages;

        $m = new \ReflectionMethod($this->target, 'injectComponents');
        $m->setAccessible(true);

        $actual = $m->invoke($this->target, $pages, null);

        $this->assertEquals($expect, $actual);

    }
}

