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
use Core\Entity\Status\StatusAwareEntityTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;

/**
 * Tests for \Core\Entity\Status\StatusAwareEntityTrait
 *
 * @covers \Core\Entity\Status\StatusAwareEntityTrait
 * @coversDefaultClass \Core\Entity\Status\StatusAwareEntityTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Status
 */
class StatusAwareEntityTraitTest extends TestCase
{
    use SetupTargetTrait;

    /**
     *
     *
     * @var string|Saett_Entity
     */
    private $target = Saett_Entity::class;

    /**
     * @covers ::setStatus()
     */
    public function testSetStatusUsesEntityClass()
    {
        $this->target->setStatus(Saett_Status::STATE_ONE);

        $this->assertInstanceOf(Saett_Status::class, $this->target->getStatus());
        $this->assertTrue($this->target->getStatus()->is(Saett_Status::STATE_ONE));
    }

    /**
     * @covers ::setStatus()
     */
    public function testSetStatusThrowsExceptionIfInvalidStatusInstanceIsPassed()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected object of');

        $this->target->setStatus(new Saett_InvalidStatus('invalid'));
    }

    /**
     * @covers ::setStatus()
     * @covers ::getStatus()
     */
    public function testSetSatus()
    {
        $state = new Saett_Status(Saett_Status::STATE_TWO);

        $this->target->setStatus($state);

        $this->assertSame($state, $this->target->getStatus());
    }

    /**
     * @covers ::hasStatus()
     */
    public function testHasStatusReturnsWetherAStatusIsAssigned()
    {
        $this->assertFalse($this->target->hasStatus());
        $this->target->setStatus('one');
        $this->assertTrue($this->target->hasStatus());
    }

    /**
     * @covers ::hasStatus()
     */
    public function testHasStatusReturnsFalseIfNoStatusIsAssigned()
    {
        $this->assertFalse($this->target->hasStatus('state'));
    }

    /**
     * @covers ::hasStatus()
     */
    public function testHasStatus()
    {
        $this->target->setStatus('one');
        $this->assertTrue($this->target->hasStatus('one'));
        $this->assertTrue($this->target->hasStatus(new Saett_Status('one')));
        $this->assertFalse($this->target->hasStatus('two'));
    }
}

class Saett_Entity
{
    const STATUS_ENTITY_CLASS = Saett_Status::class;

    use StatusAwareEntityTrait;
}

class Saett_Status extends AbstractStatus
{
    const STATE_ONE = 'one';
    const STATE_TWO = 'two';
}

class Saett_InvalidStatus extends AbstractStatus
{
    const INVALID_STATE = 'invalid';
}
