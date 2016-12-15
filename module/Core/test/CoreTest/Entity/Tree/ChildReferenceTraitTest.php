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
use Core\Entity\Tree\ChildReferenceTrait;
use Core\Entity\Tree\Tree;
use CoreTestUtils\TestCase\SetupTargetTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;

/**
 * Tests for \Core\Entity\Tree\ChildReferenceTrait
 * 
 * @covers \Core\Entity\Tree\ChildReferenceTrait
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Tree
 */
class ChildReferenceTraitTest extends \PHPUnit_Framework_TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = ChildReferenceMock::class;

    public function propertiesProvider()
    {
        $col = $this->getMockBuilder(ArrayCollection::class)
            ->setMethods(['toArray', 'add', 'removeElement', 'clear', 'count'])
            ->disableOriginalConstructor()
            ->getMock();

        $col->expects($this->once())->method('toArray')->willReturn([]);

        $leaf = new ConcreteChildReference();

        $noChildren = new ArrayCollection();
        $children   = new ArrayCollection([new ConcreteChildReference()]);



        return [
            [ 'children', ['default' => [], 'value' => $col, 'expect' => []]],
            [ 'child', [
                'pre' => function() use ($col, $leaf) {
                        $this->target->setChildren($col);
                        $col->expects($this->once())->method('add')->with($leaf)->willReturnSelf();
                },
                'ignore_getter' => true,
                'setter_method' => 'add*',
                'value' => $leaf
            ]],
            [ 'child', [
                'pre' => function() use ($col, $leaf) {
                        $this->target->setChildren($col);
                        $col->expects($this->once())->method('removeElement')->with($leaf)->willReturnSelf();
                    },
                'ignore_getter' => true,
                'setter_method' => 'remove*',
                'value' => $leaf
            ]],
            [ 'children', [
                'pre' => function() use ($col) {
                        $this->target->setChildren($col);
                        $col->expects($this->once())->method('clear')->willReturnSelf();
                    },
                'ignore_getter' => true,
                'setter_method' => 'clear*',
                'value' => null
            ]],
            [ 'children', [
                'pre' => function() use ($children) {
                        $this->target->setChildren($children);
                    },
                'ignore_setter' => true,
                'getter_method' => 'has*',
                'value' => true,
            ]],
            [ 'children', [
                'pre' => function() use ($noChildren) {
                        $this->target->setChildren($noChildren);
                    },
                'ignore_setter' => true,
                'getter_method' => 'has*',
                'value' => false,
            ]]
        ];
    }
}

class ChildReferenceMock
{
    use ChildReferenceTrait;
}