<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace ApplicationsTest\Factory\Listener;

use PHPUnit\Framework\TestCase;

use Applications\Factory\Listener\JobSelectValuesFactory;
use Applications\Listener\JobSelectValues;
use Applications\Paginator\JobSelectPaginator;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Applications\Factory\Listener\JobSelectValuesFactory
 *
 * @covers \Applications\Factory\Listener\JobSelectValuesFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Factory
 * @group Applications.Factory.Listener
 */
class JobSelectValuesFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     * @var array|\PHPUnit_Framework_MockObject_MockObject|JobSelectValuesFactory
     */
    private $target = [
        JobSelectValuesFactory::class,
        '@testCreateService' => [
            'mock' => [
                '__invoke' => ['@with' => 'getInvokeArgs', 'count' => 1]
            ]
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    private function getInvokeArgs()
    {
        return [$this->getServiceManagerMock(), JobSelectValues::class];
    }

    public function testCreateService()
    {
        $this->target->createService($this->getServiceManagerMock());
    }

    public function testServiceCreation()
    {
        $paginator = $this
            ->getMockBuilder(JobSelectPaginator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $paginators = $this->createPluginManagerMock(
            [JobSelectPaginator::class => $paginator],
            $this->getServiceManagerMock()
        );
        $container = $this->createServiceManagerMock(['Core/PaginatorService' => $paginators]);

        $listener = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(JobSelectValues::class, $listener);
        $this->assertAttributeSame($paginator, 'paginator', $listener);
    }
}
