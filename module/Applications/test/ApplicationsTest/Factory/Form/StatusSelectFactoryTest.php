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

use Applications\Factory\Form\StatusSelectFactory;
use Applications\Repository\Application as ApplicationsRepository;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Core\Form\Element\Select;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Applications\Factory\Form\StatusSelectFactory
 *
 * @covers \Applications\Factory\Form\StatusSelectFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Applications
 * @group Applications.Factory
 * @group Applications.Factory.Form
 */
class StatusSelectFactoryTest extends TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|StatusSelectFactory
     */
    private $target = [
        StatusSelectFactory::class,
        '@testCreateService' => [
            'mock' => [
                '__invoke' => ['@with' => 'getInvokeMockArgs','count' => 1]
            ]
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    private function getInvokeMockArgs()
    {
        return [$this->getServiceManagerMock(), Select::class];
    }

    public function testCreateService()
    {
        $this->target->createService($this->getServiceManagerMock());
    }

    public function testServiceCreation()
    {
        $states = [
            'stateOne',
            'stateTwo',
        ];

        $applications = $this
            ->getMockBuilder(ApplicationsRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStates'])
            ->getMock()
        ;
        $applications
            ->expects($this->once())
            ->method('getStates')
            ->will($this->returnValue($states))
        ;

        $repositories = $this->createPluginManagerMock(
            ['Applications' => $applications],
            $this->getServiceManagerMock()
        );

        $container = $this->createServiceManagerMock(['repositories' => $repositories]);

        $select = $this->target->__invoke($container, 'irrelevant');

        $expected = [
            '' => '',
            'stateOne' => 'stateOne',
            'stateTwo' => 'stateTwo',
        ];

        $this->assertInstanceOf(Select::class, $select);
        $this->assertEquals($expected, $select->getValueOptions());
    }
}
