<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace CoreTest\Entity;

use PHPUnit\Framework\TestCase;


use Core\Entity\AbstractIdentifiableHydratorAwareEntity;
use Core\Entity\Hydrator\EntityHydrator;

/**
 * Test the AbstractIdentifiableHydratorAwareEntity Entity
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @group  Core
 * @group  Core.Entity
 * @covers \Core\Entity\AbstractIdentifiableHydratorAwareEntity
 */
class AbstactIdentifiableHydratorAwareEntityTest extends TestCase
{
    protected $target;

    protected function setUp(): void
    {
        $this->target = new ConcreteIdentifiableHydratorAwareEntity();
    }

    public function testSetGetHydrator()
    {
        $input = new EntityHydrator();
        $this->target->setHydrator($input);
        $this->assertSame($this->target->getHydrator(), $input);
    }

    public function testSetGetIdByMethod()
    {
        $input = new EntityHydrator();
        $this->assertEquals($this->target->getHydrator(), $input);
    }
}

class ConcreteIdentifiableHydratorAwareEntity extends AbstractIdentifiableHydratorAwareEntity
{
}
