<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace JobsTest\Entity;

use PHPUnit\Framework\TestCase;

use Core\Entity\Tree\Node;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\Entity\Category;

/**
 * Tests for \Jobs\Entity\Category
 *
 * @covers \Jobs\Entity\Category
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Jobs
 * @group Jobs.Entity
 */
class CategoryTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = Category::class;

    private $inheritance = [ Node::class ];
}
