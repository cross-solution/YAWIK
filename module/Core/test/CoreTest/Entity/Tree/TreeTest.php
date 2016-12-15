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

use Core\Entity\EntityTrait;
use Core\Entity\IdentifiableEntityTrait;
use Core\Entity\Tree\Tree;
use Core\Entity\Tree\TreeInterface;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use Doctrine\Common\Collections\Collection;

/**
 * Tests for \Core\Entity\Tree\Tree
 * 
 * @covers \Core\Entity\Tree\Tree
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Core
 * @group Core.Entity
 * @group Core.Entity.Tree
 */
class TreeTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait;

    private $target = [
        ConcreteTree::class,
        '@testUsesTraits' => false,
        '@testSetupViaConstructor' => false,
    ];

    private $inheritance = [ TreeInterface::class ];

    public function propertiesProvider() {
        return [
            [ 'name', ['value' => '', 'setter_exception' => [\InvalidArgumentException::class, 'Name must not be empty']]],
            [ 'name', 'test' ],
            [ 'value', ['value' => '', 'setter_exception' => [\InvalidArgumentException::class, 'Value must not be empty']]],
            [ 'value', ['pre' => function() { $this->target->setName('TestName For Value');}, 'value' => '', 'expect' => 'testname-for-value']],
            [ 'value', 'test' ],
            [ 'priority', 10 ],
        ];
    }

    public function testUsesTraits()
    {
        $target = new \ReflectionClass(Tree::class);

        $traits = $target->getTraitNames();

        $this->assertEquals([EntityTrait::class, IdentifiableEntityTrait::class ], $traits);
    }

    public function setupViaConstructorProvider()
    {
        return [
            [ [null, null, null], [null, null, 0] ],
            [ ['test', null, null], ['test', 'test', 0] ],
            [ ['test', 'testValue', null], ['test', 'testValue', 0] ],
            [ ['test', null, 12], ['test', 'test', 12] ],
            [ ['test', 'testValue', 12], ['test', 'testValue', 12] ],
        ];
    }

    /**
     * @dataProvider setupViaConstructorProvider
     *
     * @param $args
     * @param $expected
     */
    public function testSetupViaConstructor($args, $expected)
    {
        $refl = new \ReflectionClass(ConcreteTree::class);
        $target = $refl->newInstanceArgs($args);

        $this->assertAttributeEquals($expected[0], 'name', $target, '$name does not have expected value.');
        $this->assertAttributeEquals($expected[1], 'value', $target, '$value does not have expected value.');
        $this->assertAttributeEquals($expected[2], 'priority', $target, '$priority does not have expected value.');
    }
}

class ConcreteTree extends Tree
{

    public function hasChildren()
    {
        return false;
    }


    public function getChildren()
    {
        return null;
    }
}