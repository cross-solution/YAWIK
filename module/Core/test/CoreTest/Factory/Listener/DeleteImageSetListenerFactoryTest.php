<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Factory\Listener;

use Core\Factory\Listener\DeleteImageSetListenerFactory;
use Core\Listener\DeleteImageSetListener;
use Core\Repository\RepositoryService;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Core\Factory\Listener\DeleteImageSetListenerFactory
 * 
 * @covers \Core\Factory\Listener\DeleteImageSetListenerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @group Core
 * @group Core.Factory
 * @group Core.Factory.Listener
 */
class DeleteImageSetListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = [
        DeleteImageSetListenerFactory::class,
        '@testCreateService' => ['mock' => ['__invoke' => ['@with' => 'getCreateServiceInvokationArgs', 'count' => 1]]],
    ];

    private $inheritance = [ FactoryInterface::class ];

    private function getCreateServiceInvokationArgs()
    {
        return [$this->getServiceManagerMock(), DeleteImageSetListener::class];
    }

    public function testCreateService()
    {
        $this->target->createService($this->getServiceManagerMock());
    }

    public function testInvokationWithConfig()
    {
        $repositories = $this->getMockBuilder(RepositoryService::class)->disableOriginalConstructor()->getMock();
        $config = [
            DeleteImageSetListener::class => ['is' => 'here'],
        ];

        $container = $this->getServiceManagerMock();
        $container->setService('repositories',$repositories);
        $container->setService('Config', $config);

        $listener = $this->target->__invoke($container, 'irrelevant');

        $this->assertAttributeSame($repositories, 'repositories', $listener);
        $this->assertAttributeEquals(['is' => 'here'], 'config', $listener);
    }

    public function testInvokationWithoutConfig()
    {
        $repositories = $this->getMockBuilder(RepositoryService::class)->disableOriginalConstructor()->getMock();
        $config = [];

        $container = $this->getServiceManagerMock([
                'repositories' => $repositories,
            ]);
        $container->setService('Config', $config);

        $listener = $this->target->__invoke($container, 'irrelevant');

        $this->assertAttributeSame($repositories, 'repositories', $listener);
        $this->assertAttributeEquals([], 'config', $listener);
    }


}