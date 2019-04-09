<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\View\Helper;

use PHPUnit\Framework\TestCase;

use Core\EventManager\EventManager;
use Core\View\Helper\Snippet;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\EventManager\Event;
use Zend\EventManager\ResponseCollection;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Partial;
use Zend\View\Model\ViewModel;

/**
 * Tests for \Core\View\Helper\Snippet
 *
 * @covers \Core\View\Helper\Snippet
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 */
class SnippetTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        Snippet::class,
        'getTargetArgs',
        '@testInheritance' => ['as_reflection' => true],
        '@testConstruction' => false,
    ];

    private $inheritance = [ AbstractHelper::class ];

    private function getTargetArgs()
    {
        $testName = $this->getName(false);

        $partial = $this->getMockBuilder(Partial::class)->disableOriginalConstructor()->setMethods(['__invoke'])->getMock();
        $event  = new Event();
        $responses = new ResponseCollection();
        $events = $this->getMockBuilder(EventManager::class)->disableOriginalConstructor()->setMethods(['triggerEvent', 'getEvent'])->getMock();
        $events->expects($this->any())->method('getEvent')->willReturn($event);
        $events->expects($this->any())->method('triggerEvent')->willReturn($responses);

        $config = [];
        if ('testReturnsContentFromConfigIfOnlyString' == $testName) {
            $config = [
                'test' => 'test content',
            ];
        } elseif ('testReturnsContent' == $testName) {
            $config = [
                'test' => [
                    'test content',
                    [
                        'content' => 'test content prio',
                        'priority' => -10,
                    ],
                ],
            ];
            $responses->push('event content');
            $responses->push(['content' => 'event content prio', 'priority' => 10]);
        }


        $this->args = [
            'event'  => $event,
            'responses' => $responses,
            'events' => $events,
            'partial' => $partial,
            'config'  => $config,
        ];

        return [$partial, $events, &$config];
    }

    public function testConstruction()
    {
        $partial = new Partial();
        $events  = new EventManager();
        $config  = ['it' => 'works!'];

        $instance = new Snippet($partial, $events, $config);

        $this->assertAttributeSame($partial, 'partials', $instance);
        $this->assertAttributeSame($events, 'events', $instance);
        $this->assertAttributeSame($config, 'config', $instance);
    }

    public function testReturnsEmptyString()
    {
        $event = $this->args['event'];
        $event->setName('nothingToDo');
        $event->setTarget($this->target);

        $this->assertSame('', $this->target->__invoke('nothingToDo'));
    }

    public function testReturnsContentFromConfigIfOnlyString()
    {
        $this->assertSame($this->args['config']['test'], $this->target->__invoke('test'));
    }

    public function testIgnoresNullValuesFromEventListeners()
    {
        $this->args['responses']->push(null);

        $this->assertEmpty($this->target->__invoke('test'));
    }

    public function testPassesViewModelsDirectToPartialsHelper()
    {
        $model = new ViewModel();
        $this->args['responses']->push($model);

        $this->args['partial']->expects($this->once())->method('__invoke')->with($model)->willReturn('works');

        $this->assertEquals('works', $this->target->__invoke('test'));
    }

    public function testConvertsTraversableValues()
    {
        $this->args['responses']->push(['content' => '%val%%val2%', 'values' => ['val2' => 'key2']]);

        $this->assertEquals('keykey2', $this->target->__invoke('test', new \ArrayObject(['val' => 'key'])));
    }

    public function testCallsPartialWhenTemplateKeyIsPresent()
    {
        $this->args['responses']->push(['template' => 'template']);
        $this->args['partial']->expects($this->once())->method('__invoke')->with('template', ['key' => 'val'])->willReturn('works');

        $this->assertEquals('works', $this->target->__invoke('test', ['key' => 'val']));
    }

    public function testPassesStringsWithoutSpacesToPartialsHelper()
    {
        $this->args['responses']->push('template');
        $this->args['partial']->expects($this->once())->method('__invoke')->with('template')->willReturn('works');

        $this->assertEquals('works', $this->target->__invoke('test'));
    }

    public function testReturnsContent()
    {
        $this->assertEquals('event content priotest contentevent contenttest content prio', $this->target->__invoke('test'));
    }

    public function testThrowsException()
    {
        $this->expectException('\UnexpectedValueException');

        $this->args['responses']->push([]);

        $this->target->__invoke('test');
    }
}
