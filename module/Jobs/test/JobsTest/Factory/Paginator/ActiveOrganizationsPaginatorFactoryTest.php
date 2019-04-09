<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Factory\Paginator;

use PHPUnit\Framework\TestCase;

use Core\Paginator\Adapter\DoctrineMongoCursor;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Doctrine\ODM\MongoDB\Cursor;
use Jobs\Factory\Paginator\ActiveOrganizationsPaginatorFactory;
use Jobs\Repository\Job;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Jobs\Factory\Paginator\ActiveOrganizationsPaginatorFactory
 *
 * @covers \Jobs\Factory\Paginator\ActiveOrganizationsPaginatorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Factory
 * @group Jobs.Factory.Paginator
 */
class ActiveOrganizationsPaginatorFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|ActiveOrganizationsPaginatorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        ActiveOrganizationsPaginatorFactory::class,
        '@testCreateService' => ['mock' => ['__invoke' => ['count' => 1]]],
    ];

    private $inheritance = [ FactoryInterface::class ];
    
    public function testServiceCreation()
    {
        $request = new Request();
        $request->getQuery()->set('q', 'term');


        $cursor = $this->getMockBuilder(Cursor::class)->disableOriginalConstructor()->getMock();

        $repository = $this->getMockBuilder(Job::class)->disableOriginalConstructor()->setMethods(['findActiveOrganizations'])->getMock();
        $repository->expects($this->once())->method('findActiveOrganizations')->with('term')->will($this->returnValue($cursor));

        $repositories = $this->createPluginManagerMock(['Jobs' => $repository]);

        $container = $this->createServiceManagerMock(['repositories' => $repositories, 'Request' => $request]);

        $this->target->__invoke($container, 'irrelevant');
    }
}
