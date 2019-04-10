<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Tree\AttachedLeafsTest;

use PHPUnit\Framework\TestCase;

use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\Tree\AbstractLeafs;
use Core\Entity\Tree\AttachedLeafs;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;

/**
 * Tests for \Core\Entity\Tree\AttachedLeafs
 *
 * @covers \Core\Entity\Tree\AttachedLeafs
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Tree
 */
class AttachedLeafsTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait;

    private $target = [
        AttachedLeafs::class,
        'as_reflection' => true,
    ];

    private $inheritance = [ AbstractLeafs::class, IdentifiableEntityInterface::class ];

    private $traits = [ IdentifiableEntityTrait::class ];
}
