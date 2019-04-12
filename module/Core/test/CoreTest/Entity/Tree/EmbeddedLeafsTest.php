<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Entity\Tree;

use PHPUnit\Framework\TestCase;

use Core\Entity\Tree\AbstractLeafs;
use Core\Entity\Tree\EmbeddedLeafs;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Entity\Tree\EmbeddedLeafs
 *
 * @covers \Core\Entity\Tree\EmbeddedLeafs
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Tree
 */
class EmbeddedLeafsTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = EmbeddedLeafs::class;

    private $inheritance = [ AbstractLeafs::class ];
}
