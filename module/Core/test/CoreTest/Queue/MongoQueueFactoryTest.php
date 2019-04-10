<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue;

use PHPUnit\Framework\TestCase;

use Core\Queue\MongoQueue;
use Core\Queue\MongoQueueFactory;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use SlmQueue\Job\JobPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Queue\MongoQueueFactory
 *
 * @covers \Core\Queue\MongoQueueFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoQueueFactoryTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var string|MongoQueueFactory
     */
    private $target = MongoQueueFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateServiceWithoutConfig()
    {
        $jobManager = $this->prophesize(JobPluginManager::class);
        $container = $this->prophesize(ServiceManager::class);
        $container->get('config')->willReturn([])->shouldBeCalled();
        $container->get(JobPluginManager::class)->willReturn($jobManager->reveal())->shouldBeCalled();
        $collection = $this->prophesize(\MongoDB\Collection::class)->reveal();
        $client = $this->prophesize(\MongoDB\Client::class);
        $client->selectCollection('testdb', 'core.queue')->willReturn($collection)->shouldBeCalled();
        $mongoClient = $this->prophesize(\MongoClient::class);
        $mongoClient->getClient()->willReturn($client->reveal())->shouldBeCalled();

        $dm = $this->prophesize(DocumentManager::class);

        $dm->getConnection()->willReturn(new class($mongoClient->reveal()) {
            public $client;
            public function __construct($client)
            {
                $this->client = $client;
            }
            public function getMongoClient()
            {
                return $this->client;
            }
        })->shouldBeCalled();

        $dm->getConfiguration()->willReturn(new class {
            public function getDefaultDB()
            {
                return 'testdb';
            }
        })->shouldBeCalled();
        $container->get('Core/DocumentManager')->willReturn($dm->reveal())->shouldBeCalled();

        /** @noinspection PhpParamsInspection */
        $queue = $this->target->__invoke($container->reveal(), 'irrelevant');

        $this->assertInstanceOf(MongoQueue::class, $queue);
    }

    public function testCreateServiceWithOptions()
    {
        $config = [
            'db' => 'testdb',
            'collection' => 'collection',
        ];

        $jobManager = $this->prophesize(JobPluginManager::class);
        $container = $this->prophesize(ServiceManager::class);
        $container->get('config')->willReturn(['slm_queue' => ['queues' => ['queue' => $config]]])->shouldBeCalled();
        $container->get(JobPluginManager::class)->willReturn($jobManager->reveal())->shouldBeCalled();
        $collection = $this->prophesize(\MongoDB\Collection::class)->reveal();
        $client = $this->prophesize(\MongoDB\Client::class);
        $client->selectCollection($config['db'], $config['collection'])->willReturn($collection)->shouldBeCalled();
        $mongoClient = $this->prophesize(\MongoClient::class);
        $mongoClient->getClient()->willReturn($client->reveal())->shouldBeCalled();

        $dm = $this->prophesize(DocumentManager::class);

        $dm->getConnection()->willReturn(new class($mongoClient->reveal()) {
            public $client;
            public function __construct($client)
            {
                $this->client = $client;
            }
            public function getMongoClient()
            {
                return $this->client;
            }
        })->shouldBeCalled();

        $dm->getConfiguration()->shouldNotBeCalled();
        $container->get('Core/DocumentManager')->willReturn($dm->reveal())->shouldBeCalled();

        /** @noinspection PhpParamsInspection */
        $queue = $this->target->__invoke($container->reveal(), 'queue');

        $this->assertInstanceOf(MongoQueue::class, $queue);
    }
}
