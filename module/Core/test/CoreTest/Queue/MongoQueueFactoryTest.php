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

use Doctrine\ODM\MongoDB\Configuration;
use Interop\Container\ContainerInterface;
use MongoDB\Client;
use MongoDB\Collection;
use PHPUnit\Framework\TestCase;

use Core\Queue\MongoQueue;
use Core\Queue\MongoQueueFactory;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\ODM\MongoDB\DocumentManager;
use SlmQueue\Job\JobPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Queue\MongoQueueFactory
 *
 * @covers \Core\Queue\MongoQueueFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
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
        $dm = $this->createMock(DocumentManager::class);
        $pluginManager = $this->createMock(JobPluginManager::class);
        $mongoConfig = $this->createMock(Configuration::class);
        $client = $this->createMock(Client::class);
        $collection = $this->createMock(Collection::class);

        $dm->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($mongoConfig);
        $dm->expects($this->once())
            ->method('getClient')
            ->willReturn($client);
        $mongoConfig->expects($this->once())
            ->method('getDefaultDB')
            ->willReturn('some-db');

        $client->expects($this->once())
            ->method('selectCollection')
            ->with('some-db', 'core.queue')
            ->willReturn($collection)
        ;
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(3))
            ->method('get')
            ->willReturnMap([
                ['config', []],
                ['Core/DocumentManager', $dm],
                [JobPluginManager::class, $pluginManager]
            ]);
        $target = new MongoQueueFactory();
        $queue = $target->__invoke($container, 'some-name');

        $this->assertInstanceOf(MongoQueue::class, $queue);
    }

    public function testCreateServiceWithOptions()
    {
        $config = [
            'db' => 'testdb',
            'collection' => 'collection',
        ];
        $config['slm_queue']['queues']['queue'] = $config;
        $dm = $this->createMock(DocumentManager::class);
        $pluginManager = $this->createMock(JobPluginManager::class);
        $client = $this->createMock(Client::class);
        $collection = $this->createMock(Collection::class);

        $dm->expects($this->once())
            ->method('getClient')
            ->willReturn($client);
        $client->expects($this->once())
            ->method('selectCollection')
            ->with('testdb', 'collection')
            ->willReturn($collection)
        ;
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(3))
            ->method('get')
            ->willReturnMap([
                ['config', $config],
                ['Core/DocumentManager', $dm],
                [JobPluginManager::class, $pluginManager]
            ]);
        $target = new MongoQueueFactory();
        $queue = $target->__invoke($container, 'queue');

        $this->assertInstanceOf(MongoQueue::class, $queue);
    }
}
