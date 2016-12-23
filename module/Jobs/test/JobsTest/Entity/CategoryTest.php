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
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait;

    private $target = Category::class;

    private $inheritance = [ Node::class ];
}
