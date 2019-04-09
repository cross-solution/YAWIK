<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\AbstractStatusEntity;

/**
 * Tests for Core\Entity\AbstractStatusEntity
 *
 * @covers \Core\Entity\AbstractStatusEntity
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class AbstractStatusEntityTest extends TestCase
{
    public function testConstructionThrowsExceptionIfInvalidStateIsPassed()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Unknown status name');
        new StatusEntity('invalid');
    }

    public function testConstruction()
    {
        $target = new StatusEntity(StatusEntity::STATE_VALID);

        $this->assertAttributeEquals('valid', 'name', $target);
        $this->assertAttributeEquals(2, 'order', $target);

        $target = new StatusEntity();
        $this->assertEquals('valid', (string) $target);
    }

    public function testGetStates()
    {
        $states = StatusEntity::getStates();

        $this->assertEquals(['valid', 'first', 'unsorted'], $states);
    }

    public function testStringRepresentation()
    {
        $target = new StatusEntity('valid');

        $this->assertEquals('valid', (string) $target);
    }

    public function testCheckingForStatusName()
    {
        $target = new StatusEntity('valid');

        $this->assertTrue($target->is('valid'));
        $this->assertTrue($target->is(new StatusEntity('valid')));
        $this->assertFalse($target->is('invalid'));
    }
}

class StatusEntity extends AbstractStatusEntity
{
    const STATE_VALID = 'valid';

    protected $default = 'valid';

    protected static $orderMap = [
        self::STATE_VALID => 2,
        'unsorted' => 200,
        'first' => '10',
    ];
}
