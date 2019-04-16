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

use Core\Queue\Controller\MongoQueueController;
use Core\Queue\Controller\MongoQueueControllerFactory;
use Core\Queue\Worker\MongoWorker;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests for \Core\Queue\Controller\MongoQueueControllerFactory
 *
 * @covers \Core\Queue\Controller\MongoQueueControllerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoQueueControllerFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = MongoQueueControllerFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $worker = $this->getMockBuilder(MongoWorker::class)->disableOriginalConstructor()->getMock();
        $manager = $this->getMockBuilder(QueuePluginManager::class)->disableOriginalConstructor()->getMock();
        $container = $this->getServiceManagerMock([
            MongoWorker::class => ['service' => $worker, 'count_get' => 1],
            QueuePluginManager::class => ['service' => $manager, 'count_get' => 1]
        ]);

        $controller = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(MongoQueueController::class, $controller);
        $this->assertAttributeSame($worker, 'worker', $controller);
        $this->assertAttributeSame($manager, 'queuePluginManager', $controller);
    }
}
