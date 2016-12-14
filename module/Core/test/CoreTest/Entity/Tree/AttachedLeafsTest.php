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

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\Tree\AttachedLeafs;
use Core\Entity\Tree\Tree;
use CoreTestUtils\TestCase\AssertUsesTraitsTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\Tree\AttachedLeafs
 * 
 * @covers \Core\Entity\Tree\AttachedLeafs
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class AttachedLeafsTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait, AssertUsesTraitsTrait;

    private $target = ConcreteAttachedLeafs::class;

    private $inheritance = [ EntityInterface::class, IdentifiableEntityInterface::class ];

    public function propertiesProvider()
    {
        $col = new ArrayCollection();

        return [
            [ 'items', [ 'default' => '@' . ArrayCollection::class, 'value' => $col ]]
        ];
    }

    public function testUsesProperEntityTraits()
    {
        $this->assertUsesTraits([ EntityTrait::class, IdentifiableEntityTrait::class ], AttachedLeafs::class);
    }

    public function testToStringReturnsListOfLeafNames()
    {
        $col = new ArrayCollection();
        $leaf1 = new Tree();
        $leaf1->setName('leaf-1');
        $leaf2 = new Tree();
        $leaf2->setName('leaf-2');

        $col->add($leaf1);
        $col->add($leaf2);

        $this->target->setItems($col);


        $this->assertEquals('leaf-1, leaf-2', $this->target->__toString());
    }
}

class ConcreteAttachedLeafs extends AttachedLeafs
{ }