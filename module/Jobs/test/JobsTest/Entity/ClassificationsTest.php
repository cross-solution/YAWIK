<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace JobsTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\Tree\EmbeddedLeafs;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Jobs\Entity\Classifications;

/**
 * Tests for \Jobs\Entity\Classifications
 *
 * @covers \Jobs\Entity\Classifications
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @grozp Jobs
 * @group Jobs.Entity
 */
class ClassificationsTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestSetterGetterTrait;

    private $target = Classifications::class;

    private $inheritance = [ EntityInterface::class ];

    private $traits = [ EntityTrait::class ];

    public function propertiesProvider()
    {
        $types = new EmbeddedLeafs();
        $profs = new EmbeddedLeafs();
        return [
            ['employmentTypes', ['value' => $types, 'default@' => EmbeddedLeafs::class]],
            ['professions',  ['value' => $profs, 'default@' => EmbeddedLeafs::class]],
        ];
    }
}
