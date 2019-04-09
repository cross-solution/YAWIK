<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\EventManager;

use PHPUnit\Framework\TestCase;

use Core\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManager;

/**
 * Tests for \Core\EventManager\ListenerAggregateTrait
 *
 * @covers \Core\EventManager\ListenerAggregateTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.EventManager
 */
class ListenerAggregateTraitTest extends TestCase
{

    /**
     * @testdox Uses the ListenerAggregateTrait from the Zend Framework
     */
    public function testUsesZfListenerAggregateTrait()
    {
        $reflection = new \ReflectionClass('\Core\EventManager\ListenerAggregateTrait');
        $traits = $reflection->getTraitNames();

        $this->assertTrue($reflection->isTrait());
        $this->assertEquals(['Zend\EventManager\ListenerAggregateTrait'], $traits);
    }

    public function testAttachProxiesToAttachEvents()
    {
        $events = new EventManager();

        $target = $this
            ->getMockBuilder('\Core\EventManager\ListenerAggregateTrait')
            ->setMethods(['attachEvents'])
            ->getMockForTrait();

        $target->expects($this->once())->method('attachEvents');

        $target->attach($events);
    }

    public function testAttachEventsCallsEventsProvider()
    {
        $events = new EventManager();
        $target = new Latt_Simple();


        $target->attach($events);
        $this->assertTrue($target->providerCalled);
    }

    public function testProvideEvents()
    {
        foreach ([new Latt_EventsProperty(), new Latt_EventsProvider(), new Latt_Simple()] as $i => $target) {
            $eventsSpec = [
                ['testProp', 'testProp', 12],
                [['test2', 'test3'], 'testProp2', 3],
            ];

            if (!$i) {
                $target->events = $eventsSpec;
            } else {
                $target->testEventsSpec = $eventsSpec;
            }

            $events = $this->getMockBuilder('\Zend\EventManager\EventManager')
                ->setMethods(['attach'])
                ->getMock();
            $events->expects($this->exactly(2))->method('attach')
                ->withConsecutive(
                    [$eventsSpec[0][0], [$target, $eventsSpec[0][1]], $eventsSpec[0][2]],
                    [$eventsSpec[1][0], [$target, $eventsSpec[1][1]], $eventsSpec[1][2]]
                );


            2 > $i ? $target->attach($events) : $target->attachEvents($events, $eventsSpec);
        }
    }


    public function invalidSpecProvider()
    {
        return [
            [ [ 'invalid' ] ],
            [ [ ['missing one'] ] ]
        ];
    }

    /**
     * @dataProvider invalidSpecProvider
     */
    public function testInvalidEventsSpecThrowsException($spec)
    {
        $this->expectException('\UnexpectedValueException');
        $this->expectExceptionMessage('event name');

        $target = new Latt_EventsProperty();
        $target->events = $spec;

        $target->attach(new EventManager());
    }
}

/**
 * Class BaseTestLatt
 * @TODO: [ZF3] EventManager::attach will throw exceptions if the callback is not callable
 * @package CoreTest\EventManager
 */
abstract class BaseTestLatt
{
    public function testProp()
    {
    }
    
    public function testProp2()
    {
    }
}

class Latt_Simple extends BaseTestLatt
{
    use ListenerAggregateTrait;

    public $providerCalled = false;

    protected function eventsProvider()
    {
        $this->providerCalled = true;
        return [];
    }
}

class Latt_EventsProperty extends BaseTestLatt
{
    use ListenerAggregateTrait;

    public $events = [];
}

class Latt_EventsProvider extends BaseTestLatt
{
    use ListenerAggregateTrait;

    public $testEventsSpec =[];

    protected function eventsProvider()
    {
        return $this->testEventsSpec;
    }
}
