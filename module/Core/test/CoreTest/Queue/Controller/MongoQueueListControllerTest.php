<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue\Controller;

use PHPUnit\Framework\TestCase;

use Core\Queue\Controller\MongoQueueListController;
use Core\Queue\MongoQueue;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SlmQueue\Job\AbstractJob;
use SlmQueue\Queue\QueuePluginManager;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Console;
use Zend\Mvc\Console\Controller\AbstractConsoleController;
use Zend\Mvc\Controller\Plugin\Params;

/**
 * Tests for \Core\Queue\Controller\MongoQueueListController
 *
 * @covers \Core\Queue\Controller\MongoQueueListController
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoQueueListControllerTest extends TestCase
{
    use TestInheritanceTrait;

    private $queueManager;

    private $target = [
        MongoQueueListController::class,
        'injectConstructorDependencies',
        'mock' => [
            'params'
        ],
        '@testInheritance' => [
            'as_reflection' => true,
        ],
    ];

    private $inheritance = [ AbstractConsoleController::class ];

    private function injectConstructorDependencies()
    {
        $this->queueManager = $this->getMockBuilder(QueuePluginManager::class)->disableOriginalConstructor()
            ->setMethods(['get'])->getMock();

        return [$this->queueManager];
    }

    private function setupParamsMock($name)
    {
        $params = $this->getMockBuilder(Params::class)->disableOriginalConstructor()
            ->setMethods(['fromRoute'])->getMock();

        if (is_string($name)) {
            $params->expects($this->once())->method('fromRoute')->with('queue')->willReturn($name);
        } else {
            $params->expects($this->exactly(count($name)))->method('fromRoute')
                ->will($this->returnValueMap($name));
        }

        $console = $this->getMockBuilder(AdapterInterface::class)->disableOriginalConstructor()
            ->setMethods(['writeLine'])->getMockForAbstractClass();
        $this->target->setConsole($console);

        return $params;
    }

    public function testUnsupportedQueueTypeResponse()
    {
        $queueName = 'unsupported';
        $queue = new \stdClass;
        $this->queueManager->expects($this->once())->method('get')->with($queueName)->willReturn($queue);
        $params = $this->setupParamsMock($queueName);
        $this->target->expects($this->once())->method('params')->willReturn($params);

        $out = $this->target->listAction();

        $this->assertEquals('Unsupported queue type.', $out);
    }

    public function testEmptyQueueResponse()
    {
        $queueName = 'test';
        $queue = $this->getMockBuilder(MongoQueue::class)->disableOriginalConstructor()
            ->setMethods(['listing'])->getMock();
        $this->queueManager->expects($this->once())->method('get')->with($queueName)->willReturn($queue);
        $params = $this->setupParamsMock([
            ['queue', null, $queueName],
            ['limit', 0, 10],
            ['status', null, 1]
        ]);
        $this->target->expects($this->exactly(3))->method('params')->willReturn($params);

        $queue->expects($this->once())->method('listing')->with(['limit' => 10, 'status' => 1])->willReturn(null);

        $out = $this->target->listAction();

        $this->assertEquals('Queue is empty.', $out);
    }

    public function testWritesListToConsole()
    {
        $queueName = 'test';
        $queue = $this->getMockBuilder(MongoQueue::class)->disableOriginalConstructor()
                      ->setMethods(['listing'])->getMock();
        $this->queueManager->expects($this->once())->method('get')->with($queueName)->willReturn($queue);
        $params = $this->setupParamsMock([
            ['queue', null, $queueName],
            ['limit', 0, 10],
            ['status', null, 1]
        ]);
        $this->target->expects($this->exactly(3))->method('params')->willReturn($params);

        $job = (new class extends AbstractJob {
            public function __construct()
            {
                $this->setId('test');
            }

            public function execute()
            {
                // TODO: Implement execute() method.
            }
        });
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $jobs = [
           [
               'job' => $job,
               'created' => new \MongoDb\BSON\UTCDateTime($date->getTimestamp() * 1000),
               'executed' => new \MongoDb\BSON\UTCDateTime($date->getTimestamp() * 1000),
               'scheduled' => new \MongoDb\BSON\UTCDateTime($date->getTimestamp() * 1000),
               'tried' => 10,
           ],
        ];

        $queue->expects($this->once())->method('listing')->with(['limit' => 10, 'status' => 1])->willReturn($jobs);

        $lineTmpl = '%-20s %s';
        $console = $this->target->getConsole();
        $console->expects($this->exactly(7))->method('writeLine')->withConsecutive(
            [ get_class($job) . ' [ test ]' ],
            [ sprintf($lineTmpl, 'Created', $date->format('Y-m-d H:i:s')) ],
            [ sprintf($lineTmpl, 'Executed', $date->format('Y-m-d H:i:s')) ],
            [ sprintf($lineTmpl, 'Scheduled', $date->format('Y-m-d H:i:s')) ],
            [ sprintf($lineTmpl, 'Tries', $jobs[0]['tried']) ]
        )->willReturn(null);

        $this->assertEmpty($this->target->listAction());
    }
}
