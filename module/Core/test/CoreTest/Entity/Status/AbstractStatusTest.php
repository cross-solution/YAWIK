<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Status;

use PHPUnit\Framework\TestCase;

use Core\Entity\Status\AbstractStatus;
use Core\Entity\Status\StatusInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Entity\Status\AbstractStatus
 *
 * @covers \Core\Entity\Status\AbstractStatus
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Status
 */
class AbstractStatusTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|null|Ast_ConcreteStatus
     */
    private $target = [
        Ast_ConcreteStatus::class,
        [ Ast_ConcreteStatus::TEST ],
        '@testInheritance' => [
            'class' => AbstractStatus::class,
            'as_reflection' => true
        ],
        '@testGetStates' => false,
        '@testConstructionThrowsException' => false,
        '@testConstruction' => false,
    ];

    private $inheritance = [ StatusInterface::class ];

    public function testGetStates()
    {
        $this->assertEquals(['test', 'state'], Ast_ConcreteStatus::getStates());
    }

    public function testConstructionThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid state name');

        new Ast_ConcreteStatus('invalid');
    }

    public function testConstruction()
    {
        $state = new Ast_ConcreteStatus(Ast_ConcreteStatus::TEST);

        $this->assertAttributeEquals(Ast_ConcreteStatus::TEST, 'state', $state);
    }

    public function testToString()
    {
        $this->assertEquals(Ast_ConcreteStatus::TEST, $this->target->__toString());
    }

    public function testIs()
    {
        $this->assertTrue($this->target->is(Ast_ConcreteStatus::TEST));
        $this->assertFalse($this->target->is(Ast_ConcreteStatus::STATE));
        $this->assertTrue($this->target->is(new Ast_ConcreteStatus(Ast_ConcreteStatus::TEST)));
    }
}

class Ast_ConcreteStatus extends AbstractStatus
{
    const TEST = 'test';
    const STATE = 'state';
}
