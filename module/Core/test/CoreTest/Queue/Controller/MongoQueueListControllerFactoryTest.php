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
use Core\Queue\Controller\MongoQueueListControllerFactory;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Queue\Controller\MongoQueueListControllerFactory
 *
 * @covers \Core\Queue\Controller\MongoQueueListControllerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoQueueListControllerFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = MongoQueueListControllerFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $queueManager = $this->getMockBuilder(QueuePluginManager::class)->disableOriginalConstructor()->getMock();
        $container = $this->getServiceManagerMock([
            QueuePluginManager::class => ['service' => $queueManager, 'count_get' => 1]
        ]);

        $controller = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(MongoQueueListController::class, $controller);
        $this->assertAttributeSame($queueManager, 'queuePluginManager', $controller);
    }
}
