<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace ApplicationsTest\Factory\Form;

use PHPUnit\Framework\TestCase;

use Applications\Factory\Form\JobSelectFactory;
use Applications\Form\Element\JobSelect;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Job;
use Jobs\Repository\Job as JobRepository;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Applications\Factory\Form\JobSelectFactory
 *
 * @covers \Applications\Factory\Form\JobSelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Factory
 * @group Applications.Factory.Form
 */
class JobSelectFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    private $target = [
        JobSelectFactory::class,
        '@testCreateService' => [
            'mock' => [
                '__invoke' => ['@with' => 'getServiceManagerMock', 'count' => 1]
            ]
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $forms = $this->getServiceManagerMock();
        $this->target->createService($forms);
    }

    public function provideServiceCreationArgs()
    {
        return [
            [null], ['TestJob']
        ];
    }

    /**
     * @dataProvider provideServiceCreationArgs
     *
     * @param string|null $jobId
     */
    public function testServiceCreation($jobId)
    {
        $request = new Request();
        $query   = $request->getQuery();
        $query->set('job', $jobId);
        $services = ['Request' => $request];

        if ($jobId) {
            $job = new Job();
            $job->setId($jobId);
            $job->setTitle('TestTitle');

            $repository = $this->getMockBuilder(JobRepository::class)->disableOriginalConstructor()
                ->setMethods(['find'])->getMock();
            $repository->expects($this->once())->method('find')->with($jobId)->will($this->returnValue($job));

            $repositories = $this->createPluginManagerMock(['Jobs' => $repository], $this->getServiceManagerMock());
            $services['repositories'] = ['service' => $repositories, 'count' => 1];
        } else {
            $services['repositories'] = ['service' => null, 'count' => 0];
        }
        $container = $this->createServiceManagerMock($services);
        $select = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(JobSelect::class, $select);
        if ($jobId) {
            $this->assertEquals([0 => '', 'TestJob' => 'TestTitle'], $select->getValueOptions());
        }
    }
}
