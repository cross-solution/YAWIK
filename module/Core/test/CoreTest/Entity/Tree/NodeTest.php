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

use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\Tree\Node;
use Core\Entity\Tree\NodeInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;

/**
 * Tests for \Core\Entity\Tree\Node
 *
 * @covers \Core\Entity\Tree\Node
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Tree
 */
class NodeTest extends TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait, TestSetterGetterTrait;

    /**
     *
     *
     * @var string|Node
     */
    private $target = [
        Node::class,
        '@testConstruction' => false,
        '@testValueFilter' => false,
    ];

    private $inheritance = [ NodeInterface::class ];

    private $traits = [ EntityTrait::class, IdentifiableEntityTrait::class ];

    public function propertiesProvider()
    {
        return [
            ['name', ['value' => '', 'setter_exception' => ['\InvalidArgumentException', 'Name must not be empty']]],
            ['name', 'testName'],
            ['value', ['value' => '', 'setter_exception' => ['\InvalidArgumentException', 'Value must not be empty']]],
            ['value', 'testValue'],
            ['value', ['pre' => function () {
                $this->target->setName('test Name');
            }, 'value' => '', 'expect' => 'test_name']],
            ['value', ['pre' => function () {
                $this->target->setName('test-Name');
            }, 'value' => '', 'expect' => 'test_name']],
            ['value', ['pre' => function () {
                $this->target->setName('testName');
            }, 'value' => '', 'expect' => 'testname']],
            ['value', ['pre' => function () {
                $this->target->setName('test Name');
            }, 'ignore_setter' => true, 'value' => 'test_name']],
            ['priority', 12],
            ['children', ['default' => new ArrayCollection(), 'value' => new ArrayCollection()]],
            ['children', ['value' => new ArrayCollection([new Node('test')]), 'getter_method' => 'has*', 'expect' => true]],
            ['children', ['value' => new ArrayCollection(), 'getter_method' => 'has*', 'expect' => false]],
            ['parent', new Node('parent')],
        ];
    }

    public function testClearChildren()
    {
        $children = new ArrayCollection([new Node('child')]);
        $this->target->setChildren($children);
        $this->assertSame($this->target, $this->target->clearChildren(), 'Fluent interface broken.');
        $this->assertEmpty($children->count());
    }

    public function testAddChild()
    {
        $child = $this->getMockBuilder(Node::class)->setMethods(['setParent'])->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('setParent')->with($this->target);

        $children = $this->getMockBuilder(ArrayCollection::class)->setMethods(['add'])->getMock();
        $children->expects($this->once())->method('add')->with($child);

        $this->target->setChildren($children)->addChild($child);
    }

    public function testRemoveChild()
    {
        $child = new Node('child');
        $children = $this->getMockBuilder(ArrayCollection::class)->setMethods(['removeElement'])->getMock();
        $children->expects($this->once())->method('removeElement')->with($child);

        $this->target->setChildren($children)->removeChild($child);
    }

    public function testSetChildrenSetsParent()
    {
        $child = $this->getMockBuilder(Node::class)->setMethods(['setParent'])->disableOriginalConstructor()->getMock();
        $child->expects($this->once())->method('setParent')->with($this->target);

        $children = new ArrayCollection([$child]);

        $this->target->setChildren($children);
    }

    public function testConstruction()
    {
        $name = 'Test Name';
        $value = 'testValue';
        $prio  = 12;

        $node = new Node($name, $value, $prio);
        $this->assertEquals($name, $node->getName(), 'Name is not set correctly.');
        $this->assertEquals($value, $node->getValue(), 'Value is not set correctly.');
        $this->assertEquals($prio, $node->getPriority(), 'Priority is not set correctly.');
    }

    public function testGetValueWithParents()
    {
        $root = new Node('root');
        $child1 = new Node('child');
        $child1->setParent($root);

        $this->target->setName('gChild')->setParent($child1);

        $this->assertEquals('child-gchild', $this->target->getValueWithParents());
        $this->assertEquals('root-child-gchild', $this->target->getValueWithParents(true));
    }

    public function testGetNameWithParents()
    {
        $root = new Node('Root');
        $child1 = new Node('Child Number One');
        $child1->setParent($root);

        $this->target->setName('Child Number Two')->setParent($child1);

        $this->assertEquals('Child Number One | Child Number Two', $this->target->getNameWithParents());
        $this->assertEquals('Root | Child Number One | Child Number Two', $this->target->getNameWithParents(true));
    }

    public function testValueFilter()
    {
        $value    = 'This is aäöüÄßÖ ÜÜ 12 -Test # +fo?r THE val;üe: Filter';
        $expected = 'this_is_aaeoeueaessoe_ueue_12_test_fo_r_the_val_uee_filter';

        $this->assertEquals($expected, Node::filterValue($value));
    }
}
