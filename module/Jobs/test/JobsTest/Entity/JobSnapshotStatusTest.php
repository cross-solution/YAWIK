<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\Status\AbstractStatus;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\JobSnapshotStatus;

/**
 * Tests for \Jobs\Entity\JobSnapshotStatus
 *
 * @covers \Jobs\Entity\JobSnapshotStatus
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class JobSnapshotStatusTest extends TestCase
{
    use TestInheritanceTrait;

    /**
     *
     *
     * @var array|\ReflectionClass
     */
    private $target = [
        JobSnapshotStatus::class,
        'as_reflection' => true,
    ];

    private $inheritance = [ AbstractStatus::class ];

    public function testStatusConstants()
    {
        $expect = [
            'ACTIVE' => 'active',
            'ACCEPTED' => 'accepted',
            'REJECTED' => 'rejected',
        ];
        $actual = $this->target->getConstants();

        $this->assertEquals($expect, $actual);
    }
}
