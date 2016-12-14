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

use Core\Entity\Tree\ChildReference;
use Core\Entity\Tree\ChildReferenceInterface;
use Core\Entity\Tree\ChildReferenceTrait;
use Core\Entity\Tree\Tree;
use CoreTestUtils\TestCase\AssertUsesTraitsTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;

/**
 * Tests for \Core\Entity\Tree\ChildReference
 * 
 * @covers \Core\Entity\Tree\ChildReference
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class ChildReferenceTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, AssertUsesTraitsTrait;

    private $target = ConcreteChildReference::class;

    private $inheritance = [ Tree::class, ChildReferenceInterface::class ];

    public function testUsesProperTrait()
    {
        $this->assertUsesTraits([ ChildReferenceTrait::class ], ChildReference::class);
    }
}

