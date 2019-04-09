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

use Core\Entity\Status\AbstractSortableStatus;
use Core\Entity\Status\AbstractStatus;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Entity\Status\AbstractSortableStatus
 *
 * @covers \Core\Entity\Status\AbstractSortableStatus
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Status
 */
class AbstractSortableStatusTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        false,
        '@testInheritance' => [
            AbstractSortableStatus::class,
            'as_reflection' => true,
        ],
    ];

    private $inheritance = [ AbstractStatus::class ];

    public function xtestGetStatesSortsMap()
    {
        $expect = Asst_ConcreteSortableStatus::$sortMap;
        asort($expect, SORT_NUMERIC);
        $expect = array_keys($expect);

        $this->assertEquals($expect, Asst_ConcreteSortableStatus::getStates());
    }

    public function testConstructionSetsCorrectSortValue()
    {
        $state1 = new Asst_ConcreteSortableStatus(Asst_ConcreteSortableStatus::STATE_ONE);
        $state2 = new Asst_ConcreteSortableStatus(Asst_ConcreteSortableStatus::STATE_TWO);

        $this->assertAttributeEquals(1, 'sort', $state1);
        $this->assertAttributeEquals(2, 'sort', $state2);
    }

    public function testExceptionIsThrownOnConstructionIfNoSortMapIsDefined()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(sprintf('%s does not define', Asst_InvalidSortableStatus::class));

        $state = new Asst_InvalidSortableStatus(Asst_InvalidSortableStatus::STATE_ONE);
    }

    public function testExceptionIsThrownOnGetStatesIfNoSortMapIsDefined()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage(sprintf('%s does not define', Asst_InvalidSortableStatus::class));

        Asst_InvalidSortableStatus::getStates();
    }
}

class Asst_ConcreteSortableStatus extends AbstractSortableStatus
{
    const STATE_ONE = 'one';
    const STATE_TWO = 'two';

    public static $sortMap = [
        self::STATE_TWO => 2,
        self::STATE_ONE => 1,
    ];
}

class Asst_InvalidSortableStatus extends AbstractSortableStatus
{
    const STATE_ONE = 'one';
}
