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
 *  
 */
class ChildReferenceTraitTest extends \PHPUnit_Framework_TestCase
{
    use SetupTargetTrait, TestSetterGetterTrait;

    private $target = ChildReferenceMock::class;

    public function propertiesProvider()
    {
        $col = $this->getMockBuilder(ArrayCollection::class)
            ->setMethods(['toArray', 'add', 'remove', 'clear'])
            ->disableOriginalConstructor()
            ->getMock();

        $col->expects($this->once())->method('toArray')->willReturn([]);

        $leaf = new Tree();



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
                        $col->expects($this->once())->method('remove')->with($leaf)->willReturnSelf();
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
            ]]
        ];
    }
}

class ChildReferenceMock
{
    use ChildReferenceTrait;
}