<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace AuthTest\Entity;

use PHPUnit\Framework\TestCase;

use Auth\Entity\Status;
use Jobs\Entity\StatusInterface;
use Zend\I18n\Translator\TranslatorInterface as Translator;

/**
 * Tests for Status
 *
 * @covers \Auth\Entity\Status
 * @coversDefaultClass \Auth\Entity\Status
 *
 * @author fedys
 */
class StatusTest extends TestCase
{
    /**
     * The "Class under Test"
     *
     * @var Status
     */
    private $target;

    protected function setUp(): void
    {
        $this->target = new Status();
    }

    /**
     * @testdox Extends \Core\Entity\AbstractEntity and implements \Jobs\Entity\StatusInterface
     * @coversNothing
     */
    public function testExtendsAbstractEntityAndImplementsStatusInterface()
    {
        $this->assertInstanceOf('\Core\Entity\AbstractEntity', $this->target);
        $this->assertInstanceOf('\Jobs\Entity\StatusInterface', $this->target);
    }

    /**
     * @testdox      Can be constructed in all possible states
     * @dataProvider provideCreatingInstancesTestData
     * @covers ::__construct
     * @covers \Auth\Entity\Status::getName
     * @covers \Auth\Entity\Status::getOrder
     *
     * @param string $status           the status to set
     * @param string $expectedName     the expected name for the status
     * @param string $expectedOrder    the expected order for the status
     */
    public function testCreatingInstances($status, $expectedName, $expectedOrder)
    {
        $target = new Status($status);

        $this->assertAttributeEquals($expectedName, 'name', $target);
        $this->assertAttributeEquals($expectedOrder, 'order', $target);
    }

    /**
     * @dataProvider provideInvalidCreatingInstancesTestData
     * @expectedException DomainException
     * @expectedExceptionMessage Unknown status:
     */
    public function testStatusThrowsExceptionIfInvalidStatusPassed($status)
    {
        new Status($status);
    }

    public function testGetStates()
    {
        $expected = [
            'active',
            'inactive'
        ];
        $this->assertEquals($expected, $this->target->getStates());
    }
    
    public function testGetOptions()
    {
        $states = $this->target->getStates();

        $translator = $this->getMockBuilder(Translator::class)
            ->setMethods(['translate', 'translatePlural'])
            ->getMock();
        $translator->expects($this->exactly(count($states)))
            ->method('translate')
            ->willReturnArgument(0);
        
        $result = $this->target->getOptions($translator);
        $this->assertSame($states, array_values($result));
        $this->assertSame($states, array_keys($result));
    }

    public function testToString()
    {
        $state = new Status(StatusInterface::INACTIVE);
        $this->assertEquals(StatusInterface::INACTIVE, (string) $state);
    }
    
    public function provideCreatingInstancesTestData()
    {
        return [
            [
                "active",
                Status::ACTIVE,
                50
            ],
            [
                "inactive",
                Status::INACTIVE,
                60
            ]
        ];
    }
    
    public function provideInvalidCreatingInstancesTestData()
    {
        return [
            [
                "aCtive",
            ],
            [
                "inactiVe",
            ],
            [
                "highly invalid status name",
            ],
        ];
    }
}
