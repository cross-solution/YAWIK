<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Factory\Paginator;

use PHPUnit\Framework\TestCase;


use Applications\Factory\Paginator\JobSelectPaginatorFactory;
use Applications\Paginator\JobSelectPaginator;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Repository\Job;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Applications\Factory\Paginator\JobSelectPaginatorFactory
 *
 * @covers \Applications\Factory\Paginator\JobSelectPaginatorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Factory
 * @group Applications.Factory.Paginator
 */
class JobSelectValuesFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|JobSelectPaginatorFactory
     */
    private $target = [
        JobSelectPaginatorFactory::class,
        '@testCreateService' => [
            'mock' => [
                '__invoke' => ['@with' => 'getInvokeArgs', 'count' => 1]
            ]
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    private function getInvokeArgs()
    {
        return [$this->getServiceManagerMock(), JobSelectPaginator::class];
    }

    public function testCreateService()
    {
        $this->target->createService($this->getServiceManagerMock());
    }

    public function testServiceCreation()
    {
        $repository = $this
            ->getMockBuilder(Job::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repositories = $this
            ->createPluginManagerMock(
                ['Jobs' => $repository],
                $this->getServiceManagerMock()
            )
        ;
        $container = $this->createServiceManagerMock(['repositories' => $repositories]);

        $paginator = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(JobSelectPaginator::class, $paginator);
        $this->assertAttributeSame($repository, 'repository', $paginator);
    }
}
